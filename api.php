<?php
session_start();
require_once 'SessionManager.php';

header('Content-Type: application/json');

$action = $_POST['action'] ?? $_GET['action'] ?? '';
$manager = new SessionManager();

try {
    switch ($action) {
        case 'createSession':
            $sessionKey = $manager->createSession();
            echo json_encode(['success' => true, 'sessionKey' => $sessionKey]);
            break;
            
        case 'joinSession':
            $sessionKey = $_POST['sessionKey'] ?? '';
            $participantName = $_POST['participantName'] ?? '';
            
            $participantId = $manager->joinSession($sessionKey, $participantName);
            if ($participantId) {
                $_SESSION['participantId'] = $participantId;
                $_SESSION['sessionKey'] = $sessionKey;
                $_SESSION['participantName'] = $participantName;
                echo json_encode(['success' => true, 'participantId' => $participantId]);
            } else {
                echo json_encode(['success' => false, 'error' => 'Session not found or inactive']);
            }
            break;
            
        case 'buzz':
            $sessionKey = $_POST['sessionKey'] ?? '';
            $participantId = $_POST['participantId'] ?? '';
            $answer = $_POST['answer'] ?? null;
            
            $success = $manager->recordBuzz($sessionKey, $participantId, $answer);
            echo json_encode(['success' => $success]);
            break;
            
        case 'resetBuzzers':
            $sessionKey = $_POST['sessionKey'] ?? '';
            $success = $manager->resetBuzzers($sessionKey);
            echo json_encode(['success' => $success]);
            break;
            
        case 'awardPoint':
            $sessionKey = $_POST['sessionKey'] ?? '';
            $participantId = $_POST['participantId'] ?? '';
            $success = $manager->awardPoint($sessionKey, $participantId);
            echo json_encode(['success' => $success]);
            break;
            
        case 'removePoint':
            $sessionKey = $_POST['sessionKey'] ?? '';
            $participantId = $_POST['participantId'] ?? '';
            $success = $manager->removePoint($sessionKey, $participantId);
            echo json_encode(['success' => $success]);
            break;
            
        case 'endSession':
            $sessionKey = $_POST['sessionKey'] ?? '';
            $success = $manager->endSession($sessionKey);
            echo json_encode(['success' => $success]);
            break;
            
        case 'getSession':
            $sessionKey = $_GET['sessionKey'] ?? '';
            $manager->cleanupInactiveParticipants($sessionKey);
            $session = $manager->getSession($sessionKey);
            
            if ($session) {
                echo json_encode(['success' => true, 'session' => $session]);
            } else {
                echo json_encode(['success' => false, 'error' => 'Session not found']);
            }
            break;
            
        case 'pollUpdates':
            $sessionKey = $_GET['sessionKey'] ?? '';
            $lastUpdate = intval($_GET['lastUpdate'] ?? 0);
            $participantId = $_GET['participantId'] ?? null;
            
            // Ping participant to keep them active
            if ($participantId) {
                $manager->pingParticipant($sessionKey, $participantId);
            }
            
            // Long polling - wait for updates
            $timeout = 30; // 30 seconds timeout
            $startTime = time();
            
            while ((time() - $startTime) < $timeout) {
                $currentUpdate = $manager->getLastUpdate($sessionKey);
                
                if ($currentUpdate > $lastUpdate) {
                    $manager->cleanupInactiveParticipants($sessionKey);
                    $session = $manager->getSession($sessionKey);
                    echo json_encode([
                        'success' => true,
                        'session' => $session,
                        'lastUpdate' => $currentUpdate
                    ]);
                    exit;
                }
                
                usleep(500000); // Sleep for 0.5 seconds
            }
            
            // Timeout - return current state
            $session = $manager->getSession($sessionKey);
            echo json_encode([
                'success' => true,
                'session' => $session,
                'lastUpdate' => $manager->getLastUpdate($sessionKey)
            ]);
            break;
            
        default:
            echo json_encode(['success' => false, 'error' => 'Invalid action']);
    }
} catch (Exception $e) {
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
