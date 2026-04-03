<?php
class Event extends Model
{
    protected $table = 'events';

    /**
     * Get all upcoming events
     */
    public function getAllUpcomingEvents(int $limit = 50, int $offset = 0): array
    {
        $sql = "SELECT 
                    e.*,
                    u.first_name as creator_first_name,
                    u.last_name as creator_last_name,
                    u.email as creator_email,
                    c.name as club_name,
                    (SELECT COUNT(*) FROM event_attendees WHERE event_id = e.id) as attendee_count
                FROM events e
                INNER JOIN users u ON e.creator_id = u.id
                LEFT JOIN clubs c ON e.club_id = c.id
                WHERE e.status IN ('upcoming', 'ongoing')
                ORDER BY e.event_date ASC
                LIMIT :limit OFFSET :offset";
        
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Get all events by status
     */
    public function getEventsByStatus(string $status, int $limit = 50, int $offset = 0): array
    {
        $sql = "SELECT 
                    e.*,
                    u.first_name as creator_first_name,
                    u.last_name as creator_last_name,
                    u.email as creator_email,
                    c.name as club_name,
                    (SELECT COUNT(*) FROM event_attendees WHERE event_id = e.id) as attendee_count
                FROM events e
                INNER JOIN users u ON e.creator_id = u.id
                LEFT JOIN clubs c ON e.club_id = c.id
                WHERE e.status = :status
                ORDER BY e.event_date ASC
                LIMIT :limit OFFSET :offset";
        
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':status', $status, PDO::PARAM_STR);
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Get events created by a specific user
     */
    public function getEventsByCreator(int $userId): array
    {
        $sql = "SELECT 
                    e.*,
                    c.name as club_name,
                    (SELECT COUNT(*) FROM event_attendees WHERE event_id = e.id) as attendee_count
                FROM events e
                LEFT JOIN clubs c ON e.club_id = c.id
                WHERE e.creator_id = ?
                ORDER BY e.event_date DESC";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$userId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Get events for a specific club
     */
    public function getEventsByClub(int $clubId): array
    {
        $sql = "SELECT 
                    e.*,
                    u.first_name as creator_first_name,
                    u.last_name as creator_last_name,
                    (SELECT COUNT(*) FROM event_attendees WHERE event_id = e.id) as attendee_count
                FROM events e
                INNER JOIN users u ON e.creator_id = u.id
                WHERE e.club_id = ?
                ORDER BY e.event_date ASC";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$clubId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Get single event by ID with full details
     */
    public function getEventById(int $eventId): ?array
    {
        $sql = "SELECT 
                    e.*,
                    u.first_name as creator_first_name,
                    u.last_name as creator_last_name,
                    u.email as creator_email,
                    c.name as club_name,
                    (SELECT COUNT(*) FROM event_attendees WHERE event_id = e.id) as attendee_count
                FROM events e
                INNER JOIN users u ON e.creator_id = u.id
                LEFT JOIN clubs c ON e.club_id = c.id
                WHERE e.id = ?";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$eventId]);
        $event = $stmt->fetch(PDO::FETCH_ASSOC);
        return $event ?: null;
    }

    /**
     * Create a new event
     */
    public function createEvent(array $data): ?int
    {
        $sql = "INSERT INTO events (title, description, creator_id, club_id, event_date, location, category, max_attendees, image_url, google_form_url, status)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        
        $stmt = $this->db->prepare($sql);
        $result = $stmt->execute([
            $data['title'] ?? null,
            $data['description'] ?? null,
            $data['creator_id'] ?? null,
            $data['club_id'] ?? null,
            $data['event_date'] ?? null,
            $data['location'] ?? null,
            $data['category'] ?? null,
            $data['max_attendees'] ?? null,
            $data['image_url'] ?? null,
            $data['google_form_url'] ?? null,
            $data['status'] ?? 'upcoming'
        ]);
        
        return $result ? $this->db->lastInsertId() : null;
    }

    /**
     * Update event details
     */
    public function updateEvent(int $eventId, array $data): bool
    {
        $updates = [];
        $params = [];
        
        foreach ($data as $key => $value) {
            if (in_array($key, ['title', 'description', 'event_date', 'location', 'category', 'max_attendees', 'image_url', 'google_form_url', 'status'])) {
                $updates[] = "$key = ?";
                $params[] = $value;
            }
        }
        
        if (empty($updates)) {
            return false;
        }
        
        $params[] = $eventId;
        $sql = "UPDATE events SET " . implode(', ', $updates) . " WHERE id = ?";
        
        $stmt = $this->db->prepare($sql);
        return $stmt->execute($params);
    }

    /**
     * Delete event
     */
    public function deleteEvent(int $eventId): bool
    {
        $sql = "DELETE FROM events WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$eventId]);
    }

    /**
     * Register user for event
     */
    public function registerUserForEvent(int $eventId, int $userId): bool
    {
        $sql = "INSERT INTO event_attendees (event_id, user_id) VALUES (?, ?)
                ON DUPLICATE KEY UPDATE registered_at = NOW()";
        
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$eventId, $userId]);
    }

    /**
     * Unregister user from event
     */
    public function unregisterUserFromEvent(int $eventId, int $userId): bool
    {
        $sql = "DELETE FROM event_attendees WHERE event_id = ? AND user_id = ?";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$eventId, $userId]);
    }

    /**
     * Check if user is registered for event
     */
    public function isUserRegistered(int $eventId, int $userId): bool
    {
        $sql = "SELECT COUNT(*) as count FROM event_attendees WHERE event_id = ? AND user_id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$eventId, $userId]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result && (int)$result['count'] > 0;
    }

    /**
     * Get event attendees
     */
    public function getEventAttendees(int $eventId): array
    {
        $sql = "SELECT 
                    u.*,
                    ea.registered_at
                FROM event_attendees ea
                INNER JOIN users u ON ea.user_id = u.id
                WHERE ea.event_id = ?
                ORDER BY ea.registered_at DESC";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$eventId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Get events user is registered for
     */
    public function getEventsForUser(int $userId): array
    {
        $sql = "SELECT 
                    e.*,
                    u.first_name as creator_first_name,
                    u.last_name as creator_last_name,
                    c.name as club_name
                FROM events e
                INNER JOIN event_attendees ea ON e.id = ea.event_id
                INNER JOIN users u ON e.creator_id = u.id
                LEFT JOIN clubs c ON e.club_id = c.id
                WHERE ea.user_id = ?
                ORDER BY e.event_date ASC";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$userId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
