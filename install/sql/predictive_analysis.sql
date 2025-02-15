-- Create table for storing server metrics
CREATE TABLE IF NOT EXISTS `server_metrics` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `host` varchar(255) NOT NULL,
  `metrics` text NOT NULL,
  `collected_at` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `host_collected_at` (`host`, `collected_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Create table for storing predictions
CREATE TABLE IF NOT EXISTS `server_predictions` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `server_id` int(11) NOT NULL,
  `predictions` text NOT NULL,
  `created_at` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `server_id` (`server_id`),
  KEY `created_at` (`created_at`),
  CONSTRAINT `fk_server_predictions_server` FOREIGN KEY (`server_id`) REFERENCES `app_servers` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Add indexes to existing alerts table if they don't exist
ALTER TABLE `app_servers_alerts` 
ADD COLUMN IF NOT EXISTS `severity` varchar(20) NOT NULL AFTER `message`,
ADD COLUMN IF NOT EXISTS `created_at` datetime NOT NULL AFTER `severity`,
ADD INDEX IF NOT EXISTS `idx_severity` (`severity`),
ADD INDEX IF NOT EXISTS `idx_created_at` (`created_at`);
