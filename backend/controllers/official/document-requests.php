<?php
declare(strict_types=1);

require_once dirname(__DIR__, 2) . '/bootstrap.php';
require_once __DIR__ . '/guard.php';

$documentRequestNotice = '';
$documentRequestError = '';

if (($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'POST') {
    $submittedToken = (string) ($_POST['csrf_token'] ?? '');
    $documentRequestTab = document_request_tab((string) ($_POST['tab'] ?? 'all'));
    $documentRequestSearch = trim((string) ($_POST['q'] ?? ''));
    $requestId = filter_var($_POST['request_id'] ?? null, FILTER_VALIDATE_INT) ?: 0;
    $action = (string) ($_POST['action'] ?? 'status');

    if (!hash_equals($csrf, $submittedToken)) {
        $documentRequestError = 'Your session security token expired. Please try again.';
    } else {
        $result = $action === 'claim'
            ? claim_official_document_request($requestId, (int) current_user_id(), $_POST)
            : update_official_document_request_status($requestId, (string) ($_POST['status'] ?? ''), (int) current_user_id());

        if ($result['ok']) {
            $params = ['status' => $documentRequestTab];
            $params[$action === 'claim' ? 'claimed' : 'updated'] = 1;
            if ($documentRequestSearch !== '') {
                $params['q'] = $documentRequestSearch;
            }
            header('Location: document-requests.php?' . http_build_query($params));
            exit;
        }

        $documentRequestError = $result['error'] ?? 'Unable to update this document request.';
    }
} else {
    $documentRequestTab = document_request_tab((string) ($_GET['status'] ?? 'all'));
    $documentRequestSearch = trim((string) ($_GET['q'] ?? ''));

    if (isset($_GET['claimed'])) {
        $documentRequestNotice = 'Document claimed and archived successfully.';
    } elseif (isset($_GET['updated'])) {
        $documentRequestNotice = 'Document request status updated successfully.';
    }
}

$documentRequests = list_official_document_requests($documentRequestTab, $documentRequestSearch);
$documentRequestStats = official_document_request_stats();

$documentRequestTabs = [
    'all' => ['label' => 'All', 'count' => $documentRequestStats['total']],
    'pending' => ['label' => 'Pending', 'count' => $documentRequestStats['pending']],
    'processing' => ['label' => 'Processing', 'count' => $documentRequestStats['processing']],
    'ready' => ['label' => 'Ready', 'count' => $documentRequestStats['ready']],
    'rejected' => ['label' => 'Rejected', 'count' => $documentRequestStats['rejected']],
];

$documentRequestTabUrl = static function (string $tab) use ($documentRequestSearch): string {
    $params = ['status' => $tab];
    if ($documentRequestSearch !== '') {
        $params['q'] = $documentRequestSearch;
    }

    return 'document-requests.php?' . http_build_query($params);
};

$documentRequestArchived = $documentRequestTab === 'archived';
$documentRequestReceiptDate = date('M j, Y g:i A');
