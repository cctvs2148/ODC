<?php
if (!defined('APP_INIT')) {
    exit;
}
function render_sidebar($role, $active = '') {
    $menu = [
        'admin' => [
            ['url' => '/ODC/admin/dashboard.php', 'icon' => 'bi-speedometer2', 'label' => 'Dashboard'],
            ['url' => '/ODC/admin/manage_students.php', 'icon' => 'bi-people', 'label' => 'Students'],
            ['url' => '/ODC/admin/manage_managers.php', 'icon' => 'bi-person-badge', 'label' => 'Managers'],
            ['url' => '/ODC/admin/manage_placement.php', 'icon' => 'bi-building', 'label' => 'Placement Heads'],
            ['url' => '/ODC/admin/manage_hotels.php', 'icon' => 'bi-house', 'label' => 'Hotels'],
            ['url' => '/ODC/admin/view_applications.php', 'icon' => 'bi-list-check', 'label' => 'Applications'],
            ['url' => '/ODC/admin/analytics.php', 'icon' => 'bi-bar-chart', 'label' => 'Analytics'],
        ],
        'placement' => [
            ['url' => '/ODC/placement_head/dashboard.php', 'icon' => 'bi-speedometer2', 'label' => 'Dashboard'],
            ['url' => '/ODC/placement_head/create_vacancy.php', 'icon' => 'bi-badge-ad', 'label' => 'Create Vacancy'],
            ['url' => '/ODC/placement_head/vacancies.php', 'icon' => 'bi-list', 'label' => 'Vacancies'],
        ],
        'student' => [
            ['url' => '/ODC/student/dashboard.php', 'icon' => 'bi-speedometer2', 'label' => 'Dashboard'],
            ['url' => '/ODC/student/vacancies.php', 'icon' => 'bi-list', 'label' => 'Vacancies'],
            ['url' => '/ODC/student/applications.php', 'icon' => 'bi-file-earmark-text', 'label' => 'My Applications'],
        ],
        'manager' => [
            ['url' => '/ODC/manager/dashboard.php', 'icon' => 'bi-speedometer2', 'label' => 'Dashboard'],
            ['url' => '/ODC/manager/applications.php', 'icon' => 'bi-inboxes', 'label' => 'Applications'],
        ],
        'hotelier' => [
            ['url' => '/ODC/hotelier/dashboard.php', 'icon' => 'bi-speedometer2', 'label' => 'Dashboard'],
            ['url' => '/ODC/hotelier/approved_list.php', 'icon' => 'bi-list-check', 'label' => 'Approved Students'],
        ],
    ];
    $items = $menu[$role] ?? [];
    echo '<aside class="sidebar bg-danger text-white p-3">';
    echo '<div class="sidebar-brand mb-4"><h4 class="text-yellow mb-0">ODC Hotel Duty</h4></div>';
    foreach ($items as $item) {
        $activeClass = $active === $item['url'] ? 'active' : '';
        echo '<a href="' . $item['url'] . '" class="sidebar-link d-flex align-items-center justify-content-between px-3 py-2 mb-1 rounded ' . $activeClass . '">';
        echo '<span><i class="bi ' . $item['icon'] . ' me-2"></i>' . $item['label'] . '</span>';
        echo '</a>';
    }
    echo '<a href="/ODC/ajax/logout.php" class="sidebar-link d-flex align-items-center justify-content-between px-3 py-2 mt-3 rounded bg-dark text-white"><i class="bi bi-box-arrow-right me-2"></i>Logout</a>';
    echo '</aside>';
}
function render_topbar($title) {
    echo '<div class="main-content w-100 p-4">';
    echo '<div class="d-flex align-items-center justify-content-between mb-4">';
    echo '<div><h1 class="h4 text-danger">' . htmlspecialchars($title) . '</h1></div>';
    echo '</div>';
}
function render_footer() {
    echo '</div>';
}
