-- ============================================================================
-- HOTEL RESERVATION SYSTEM - SQL QUERIES
-- ============================================================================
-- This file contains all the essential SQL queries for the hotel reservation system
-- ============================================================================

-- ============================================================================
-- 1. GET AVAILABLE ROOMS BETWEEN TWO DATES
-- ============================================================================
-- This query returns all available rooms for a specific date range
-- Parameters: @check_in_date, @check_out_date, @room_type_id (optional)
-- ============================================================================

SELECT DISTINCT r.id, r.room_number, r.status, rt.name as room_type_name, rt.price_per_night
FROM rooms r
INNER JOIN room_types rt ON r.room_type_id = rt.id
WHERE r.status = 'available'
  AND rt.is_active = 1
  AND r.id NOT IN (
    SELECT DISTINCT bd.room_id
    FROM booking_details bd
    INNER JOIN bookings b ON bd.booking_id = b.id
    WHERE bd.room_id IS NOT NULL
      AND b.status IN ('confirmed', 'checked_in')
      AND (
        (b.check_in_date <= @check_out_date AND b.check_out_date >= @check_in_date)
      )
  )
  AND (@room_type_id IS NULL OR rt.id = @room_type_id)
ORDER BY rt.price_per_night ASC;

-- Alternative: Using Laravel Eloquent equivalent
-- Room::available()
--     ->whereHas('roomType', function($q) use ($checkIn, $checkOut) {
--         $q->whereDoesntHave('bookingDetails.booking', function($q2) use ($checkIn, $checkOut) {
--             $q2->whereBetween('check_in_date', [$checkIn, $checkOut])
--                ->orWhereBetween('check_out_date', [$checkIn, $checkOut])
--                ->whereIn('status', ['confirmed', 'checked_in']);
--         });
--     })
--     ->get();

-- ============================================================================
-- 2. CREATE A NEW BOOKING
-- ============================================================================
-- This query creates a new booking with booking details
-- Note: This should be done in a transaction
-- ============================================================================

-- Step 1: Create booking
INSERT INTO bookings (
    user_id, booking_number, check_in_date, check_out_date, adults, children,
    status, total_price, tax_amount, discount_amount, final_amount,
    guest_name, guest_email, guest_phone, guest_address, special_requests,
    created_at, updated_at
) VALUES (
    @user_id,
    CONCAT('BK', DATE_FORMAT(NOW(), '%Y%m%d'), UPPER(SUBSTRING(MD5(RAND()), 1, 6))),
    @check_in_date,
    @check_out_date,
    @adults,
    @children,
    'pending',
    @total_price,
    @tax_amount,
    @discount_amount,
    @final_amount,
    @guest_name,
    @guest_email,
    @guest_phone,
    @guest_address,
    @special_requests,
    NOW(),
    NOW()
);

-- Get the booking ID
SET @booking_id = LAST_INSERT_ID();

-- Step 2: Create booking details
INSERT INTO booking_details (
    booking_id, room_type_id, room_id, quantity, price_per_night, nights, subtotal,
    created_at, updated_at
) VALUES (
    @booking_id,
    @room_type_id,
    @room_id,
    1,
    @price_per_night,
    DATEDIFF(@check_out_date, @check_in_date),
    @subtotal,
    NOW(),
    NOW()
);

-- Step 3: Update room status to reserved
UPDATE rooms SET status = 'reserved', updated_at = NOW()
WHERE id = @room_id;

-- ============================================================================
-- 3. PREVENT DOUBLE BOOKING (CHECK AVAILABILITY)
-- ============================================================================
-- This query checks if a room is available for the given date range
-- Returns 0 if available, >0 if booked
-- ============================================================================

SELECT COUNT(*) as conflict_count
FROM booking_details bd
INNER JOIN bookings b ON bd.booking_id = b.id
WHERE bd.room_id = @room_id
  AND b.status IN ('confirmed', 'checked_in')
  AND (
    (@check_in_date < b.check_out_date AND @check_out_date > b.check_in_date)
  );

-- If conflict_count > 0, the room is NOT available

-- ============================================================================
-- 4. CALCULATE TOTAL BOOKING PRICE
-- ============================================================================
-- This query calculates the total price for a booking including tax
-- Parameters: @room_type_id, @check_in_date, @check_out_date, @tax_rate
-- ============================================================================

SELECT 
    rt.id as room_type_id,
    rt.name as room_type_name,
    rt.price_per_night,
    DATEDIFF(@check_out_date, @check_in_date) as nights,
    rt.price_per_night * DATEDIFF(@check_out_date, @check_in_date) as subtotal,
    (rt.price_per_night * DATEDIFF(@check_out_date, @check_in_date) * @tax_rate / 100) as tax_amount,
    (rt.price_per_night * DATEDIFF(@check_out_date, @check_in_date) * (1 + @tax_rate / 100)) as total_price
FROM room_types rt
WHERE rt.id = @room_type_id
  AND rt.is_active = 1;

-- ============================================================================
-- 5. ADMIN DASHBOARD STATISTICS
-- ============================================================================
-- Get total bookings count
-- ============================================================================

SELECT COUNT(*) as total_bookings FROM bookings;

-- Get bookings by status
SELECT status, COUNT(*) as count
FROM bookings
GROUP BY status;

-- Get total revenue
SELECT COALESCE(SUM(amount), 0) as total_revenue
FROM payments
WHERE status = 'completed';

-- Get monthly revenue
SELECT 
    DATE_FORMAT(paid_at, '%Y-%m') as month,
    SUM(amount) as revenue
FROM payments
WHERE status = 'completed'
  AND paid_at >= DATE_SUB(NOW(), INTERVAL 12 MONTH)
GROUP BY DATE_FORMAT(paid_at, '%Y-%m')
ORDER BY month ASC;

-- ============================================================================
-- 6. TODAY'S CHECK-INS
-- ============================================================================
-- Get all bookings checking in today
-- ============================================================================

SELECT 
    b.id,
    b.booking_number,
    b.guest_name,
    b.guest_email,
    b.guest_phone,
    b.check_in_date,
    b.check_out_date,
    b.status,
    GROUP_CONCAT(CONCAT(rt.name, ' (', r.room_number, ')') SEPARATOR ', ') as rooms
FROM bookings b
LEFT JOIN booking_details bd ON b.id = bd.booking_id
LEFT JOIN room_types rt ON bd.room_type_id = rt.id
LEFT JOIN rooms r ON bd.room_id = r.id
WHERE DATE(b.check_in_date) = CURDATE()
  AND b.status IN ('confirmed', 'checked_in')
GROUP BY b.id
ORDER BY b.check_in_date ASC;

-- ============================================================================
-- 7. TODAY'S CHECK-OUTS
-- ============================================================================
-- Get all bookings checking out today
-- ============================================================================

SELECT 
    b.id,
    b.booking_number,
    b.guest_name,
    b.guest_email,
    b.guest_phone,
    b.check_in_date,
    b.check_out_date,
    b.status,
    GROUP_CONCAT(CONCAT(rt.name, ' (', r.room_number, ')') SEPARATOR ', ') as rooms
FROM bookings b
LEFT JOIN booking_details bd ON b.id = bd.booking_id
LEFT JOIN room_types rt ON bd.room_type_id = rt.id
LEFT JOIN rooms r ON bd.room_id = r.id
WHERE DATE(b.check_out_date) = CURDATE()
  AND b.status IN ('checked_in', 'checked_out')
GROUP BY b.id
ORDER BY b.check_out_date ASC;

-- ============================================================================
-- 8. MONTHLY REVENUE REPORT
-- ============================================================================
-- Get revenue report for each month
-- ============================================================================

SELECT 
    DATE_FORMAT(paid_at, '%Y-%m') as month,
    DATE_FORMAT(paid_at, '%M %Y') as month_name,
    COUNT(DISTINCT booking_id) as total_bookings,
    COUNT(*) as total_payments,
    SUM(amount) as total_revenue,
    AVG(amount) as average_payment
FROM payments
WHERE status = 'completed'
  AND paid_at >= DATE_SUB(NOW(), INTERVAL 12 MONTH)
GROUP BY DATE_FORMAT(paid_at, '%Y-%m'), DATE_FORMAT(paid_at, '%M %Y')
ORDER BY month DESC;

-- ============================================================================
-- 9. ROOM OCCUPANCY RATE
-- ============================================================================
-- Calculate room occupancy rate for a date range
-- ============================================================================

SELECT 
    rt.id,
    rt.name as room_type_name,
    rt.total_rooms,
    COUNT(DISTINCT bd.room_id) as occupied_rooms,
    (COUNT(DISTINCT bd.room_id) / rt.total_rooms * 100) as occupancy_rate
FROM room_types rt
LEFT JOIN rooms r ON rt.id = r.room_type_id
LEFT JOIN booking_details bd ON r.id = bd.room_id
LEFT JOIN bookings b ON bd.booking_id = b.id
WHERE b.status IN ('confirmed', 'checked_in')
  AND (@start_date IS NULL OR b.check_in_date <= @end_date)
  AND (@end_date IS NULL OR b.check_out_date >= @start_date)
GROUP BY rt.id, rt.name, rt.total_rooms;

-- ============================================================================
-- 10. CUSTOMER BOOKING HISTORY
-- ============================================================================
-- Get booking history for a specific customer
-- ============================================================================

SELECT 
    b.id,
    b.booking_number,
    b.check_in_date,
    b.check_out_date,
    b.status,
    b.final_amount,
    GROUP_CONCAT(DISTINCT rt.name SEPARATOR ', ') as room_types,
    GROUP_CONCAT(DISTINCT r.room_number SEPARATOR ', ') as room_numbers,
    MAX(p.amount) as paid_amount,
    MAX(p.status) as payment_status
FROM bookings b
LEFT JOIN booking_details bd ON b.id = bd.booking_id
LEFT JOIN room_types rt ON bd.room_type_id = rt.id
LEFT JOIN rooms r ON bd.room_id = r.id
LEFT JOIN payments p ON b.id = p.booking_id
WHERE b.user_id = @user_id
GROUP BY b.id, b.booking_number, b.check_in_date, b.check_out_date, b.status, b.final_amount
ORDER BY b.created_at DESC;

-- ============================================================================
-- 11. POPULAR ROOM TYPES
-- ============================================================================
-- Get most booked room types
-- ============================================================================

SELECT 
    rt.id,
    rt.name,
    rt.price_per_night,
    COUNT(bd.id) as total_bookings,
    SUM(bd.quantity) as total_rooms_booked,
    SUM(bd.subtotal) as total_revenue
FROM room_types rt
LEFT JOIN booking_details bd ON rt.id = bd.room_type_id
LEFT JOIN bookings b ON bd.booking_id = b.id
WHERE b.status != 'cancelled'
  AND (@start_date IS NULL OR b.created_at >= @start_date)
  AND (@end_date IS NULL OR b.created_at <= @end_date)
GROUP BY rt.id, rt.name, rt.price_per_night
ORDER BY total_bookings DESC
LIMIT 10;

-- ============================================================================
-- 12. UPCOMING CHECK-INS (NEXT 7 DAYS)
-- ============================================================================
-- Get upcoming check-ins for the next 7 days
-- ============================================================================

SELECT 
    b.id,
    b.booking_number,
    b.guest_name,
    b.guest_email,
    b.guest_phone,
    b.check_in_date,
    b.check_out_date,
    DATEDIFF(b.check_out_date, b.check_in_date) as nights,
    b.final_amount,
    b.status,
    GROUP_CONCAT(DISTINCT CONCAT(rt.name, ' (', r.room_number, ')') SEPARATOR ', ') as rooms
FROM bookings b
LEFT JOIN booking_details bd ON b.id = bd.booking_id
LEFT JOIN room_types rt ON bd.room_type_id = rt.id
LEFT JOIN rooms r ON bd.room_id = r.id
WHERE b.check_in_date BETWEEN CURDATE() AND DATE_ADD(CURDATE(), INTERVAL 7 DAY)
  AND b.status IN ('confirmed', 'pending')
GROUP BY b.id, b.booking_number, b.guest_name, b.guest_email, b.guest_phone, 
         b.check_in_date, b.check_out_date, b.final_amount, b.status
ORDER BY b.check_in_date ASC;

-- ============================================================================
-- END OF SQL QUERIES
-- ============================================================================

