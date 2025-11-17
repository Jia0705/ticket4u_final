-- Run this SQL to update all existing events with correct prices from their tickets
-- This is a one-time fix for events that already have tickets but missing prices

UPDATE events e
SET 
    min_price = (SELECT MIN(price) FROM ticket_types WHERE event_id = e.id),
    max_price = (SELECT MAX(price) FROM ticket_types WHERE event_id = e.id),
    total_seats = (SELECT COALESCE(SUM(quantity), 0) FROM ticket_types WHERE event_id = e.id),
    available_seats = (SELECT COALESCE(SUM(available), 0) FROM ticket_types WHERE event_id = e.id)
WHERE EXISTS (SELECT 1 FROM ticket_types WHERE event_id = e.id);
