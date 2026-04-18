<?php
session_start();

class SessionManager {
    private $dataFile = 'data/sessions.json';
    
    public function __construct() {
        if (!file_exists('data')) {
            mkdir('data', 0777, true);
        }
        if (!file_exists($this->dataFile)) {
            file_put_contents($this->dataFile, json_encode([]));
        }
    }
    
    private function getSessions() {
        $data = file_get_contents($this->dataFile);
        return json_decode($data, true) ?: [];
    }
    
    private function saveSessions($sessions) {
        file_put_contents($this->dataFile, json_encode($sessions, JSON_PRETTY_PRINT));
    }
    
    public function createSession() {
        $sessions = $this->getSessions();
        $sessionKey = $this->generateSessionKey();
        
        $sessions[$sessionKey] = [
            'sessionKey' => $sessionKey,
            'hostId' => session_id(),
            'participants' => [],
            'createdAt' => time(),
            'isActive' => true,
            'lastUpdate' => time()
        ];
        
        $this->saveSessions($sessions);
        return $sessionKey;
    }
    
    public function joinSession($sessionKey, $participantName) {
        $sessions = $this->getSessions();
        
        if (!isset($sessions[$sessionKey]) || !$sessions[$sessionKey]['isActive']) {
            return false;
        }
        
        $participantId = uniqid('p_', true);
        $sessions[$sessionKey]['participants'][$participantId] = [
            'id' => $participantId,
            'name' => $participantName,
            'joinedAt' => time(),
            'buzzedAt' => null,
            'answer' => null,
            'score' => 0,
            'lastPing' => time()
        ];
        
        $sessions[$sessionKey]['lastUpdate'] = time();
        $this->saveSessions($sessions);
        
        return $participantId;
    }
    
    public function recordBuzz($sessionKey, $participantId, $answer = null) {
        $sessions = $this->getSessions();
        
        if (!isset($sessions[$sessionKey]['participants'][$participantId])) {
            return false;
        }
        
        if ($sessions[$sessionKey]['participants'][$participantId]['buzzedAt'] === null) {
            $sessions[$sessionKey]['participants'][$participantId]['buzzedAt'] = microtime(true);
            if ($answer) {
                $sessions[$sessionKey]['participants'][$participantId]['answer'] = $answer;
            }
            $sessions[$sessionKey]['lastUpdate'] = time();
            $this->saveSessions($sessions);
            return true;
        }
        
        return false;
    }
    
    public function resetBuzzers($sessionKey) {
        $sessions = $this->getSessions();
        
        if (!isset($sessions[$sessionKey])) {
            return false;
        }
        
        foreach ($sessions[$sessionKey]['participants'] as $id => &$participant) {
            $participant['buzzedAt'] = null;
            $participant['answer'] = null;
        }
        
        $sessions[$sessionKey]['lastUpdate'] = time();
        $this->saveSessions($sessions);
        return true;
    }
    
    public function getSession($sessionKey) {
        $sessions = $this->getSessions();
        return $sessions[$sessionKey] ?? null;
    }
    
    public function awardPoint($sessionKey, $participantId) {
        $sessions = $this->getSessions();
        
        if (!isset($sessions[$sessionKey]['participants'][$participantId])) {
            return false;
        }
        
        $sessions[$sessionKey]['participants'][$participantId]['score']++;
        $sessions[$sessionKey]['lastUpdate'] = time();
        $this->saveSessions($sessions);
        return true;
    }
    
    public function removePoint($sessionKey, $participantId) {
        $sessions = $this->getSessions();
        
        if (!isset($sessions[$sessionKey]['participants'][$participantId])) {
            return false;
        }
        
        $sessions[$sessionKey]['participants'][$participantId]['score']--;
        $sessions[$sessionKey]['lastUpdate'] = time();
        $this->saveSessions($sessions);
        return true;
    }
    
    public function endSession($sessionKey) {
        $sessions = $this->getSessions();
        
        if (!isset($sessions[$sessionKey])) {
            return false;
        }
        
        $sessions[$sessionKey]['isActive'] = false;
        $sessions[$sessionKey]['lastUpdate'] = time();
        $this->saveSessions($sessions);
        return true;
    }
    
    public function pingParticipant($sessionKey, $participantId) {
        $sessions = $this->getSessions();
        
        if (!isset($sessions[$sessionKey]['participants'][$participantId])) {
            return false;
        }
        
        $sessions[$sessionKey]['participants'][$participantId]['lastPing'] = time();
        $this->saveSessions($sessions);
        return true;
    }
    
    public function cleanupInactiveParticipants($sessionKey) {
        $sessions = $this->getSessions();
        
        if (!isset($sessions[$sessionKey])) {
            return;
        }
        
        $timeout = 60; // 60 seconds timeout
        $now = time();
        $changed = false;
        
        foreach ($sessions[$sessionKey]['participants'] as $id => $participant) {
            if (($now - $participant['lastPing']) > $timeout) {
                unset($sessions[$sessionKey]['participants'][$id]);
                $changed = true;
            }
        }
        
        if ($changed) {
            $sessions[$sessionKey]['lastUpdate'] = time();
            $this->saveSessions($sessions);
        }
    }
    
    private function generateSessionKey() {
        $chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
        $key = '';
        for ($i = 0; $i < 6; $i++) {
            $key .= $chars[rand(0, strlen($chars) - 1)];
        }
        return $key;
    }
    
    public function getLastUpdate($sessionKey) {
        $sessions = $this->getSessions();
        return $sessions[$sessionKey]['lastUpdate'] ?? 0;
    }
}
