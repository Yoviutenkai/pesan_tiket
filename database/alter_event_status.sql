ALTER TABLE event
  MODIFY status_event ENUM('upcoming','ongoing','finished','cancelled') DEFAULT 'upcoming';
