-- Simple, foolproof schema
ALTER TABLE `trajet` ADD COLUMN IF NOT EXISTS `route_json` TEXT NULL AFTER `position_lng`;
ALTER TABLE `trajet` ADD COLUMN IF NOT EXISTS `current_index` INT DEFAULT 0 AFTER `route_json`;
ALTER TABLE `trajet` ADD COLUMN IF NOT EXISTS `total_points` INT DEFAULT 0 AFTER `current_index`;

-- Clear old data
TRUNCATE TABLE `trajet`;
