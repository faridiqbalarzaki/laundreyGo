<?php
namespace App\Core;

/**
 * Class View
 * Template engine sederhana berbasis PHP Native
 */
class View {
    /**
     * Render halaman view dengan layout
     * @param string $viewPath Contoh: 'dashboard/index' atau 'pos/index'
     * @param array $data Data variabel yang akan di-extract ke dalam view
     * @param string $layout 'app' (default dengan sidebar), 'auth' (login), atau 'blank'
     */
    public static function render(string $viewPath, array $data = [], string $layout = 'app'): void {
        // Ekstrak array variabel ke variabel lokal view
        extract($data);

        $viewFile = __DIR__ . '/../Views/' . $viewPath . '.php';

        if (!file_exists($viewFile)) {
            die("View file tidak ditemukan: <code>" . htmlspecialchars($viewFile) . "</code>");
        }

        // Buffer konten halaman
        ob_start();
        include $viewFile;
        $content = ob_get_clean();

        // Render sesuai layout
        if ($layout === 'petugas' || (\App\Core\Auth::isPetugas() && $layout === 'app')) {
            include __DIR__ . '/../Views/layouts/header.php';
            echo $content;
            if (file_exists(__DIR__ . '/../Views/partials/modals.php')) {
                include __DIR__ . '/../Views/partials/modals.php';
            }
            include __DIR__ . '/../Views/layouts/footer.php';
        } elseif ($layout === 'app') {
            $isKasir = \App\Core\Auth::isKasir();
            include __DIR__ . '/../Views/layouts/header.php';
            echo '<div class="app-shell">';
            include __DIR__ . '/../Views/layouts/sidebar.php';
            echo '<div class="main-viewport">';
            if (!$isKasir) {
                include __DIR__ . '/../Views/layouts/topbar.php';
            }
            $workspacePad = ($isKasir && $viewPath === 'pos/index') ? 'padding: 14px 20px 20px 24px;' : 'padding: 14px 20px 24px 24px;';
            echo '<main id="app-workspace" class="app-workspace" style="flex: 1; display: flex; flex-direction: column; overflow-y: auto; min-width: 0; ' . $workspacePad . '">';
            echo $content;
            echo '</main>';
            echo '</div>'; // .main-viewport
            echo '</div>'; // .app-shell

            // Modals & Drawers partials
            if (file_exists(__DIR__ . '/../Views/partials/modals.php')) {
                include __DIR__ . '/../Views/partials/modals.php';
            }
            if (file_exists(__DIR__ . '/../Views/partials/drawer.php')) {
                include __DIR__ . '/../Views/partials/drawer.php';
            }
            if (file_exists(__DIR__ . '/../Views/partials/receipt.php')) {
                include __DIR__ . '/../Views/partials/receipt.php';
            }

            include __DIR__ . '/../Views/layouts/footer.php';
        } elseif ($layout === 'auth') {
            include __DIR__ . '/../Views/layouts/header.php';
            echo $content;
            include __DIR__ . '/../Views/layouts/footer.php';
        } else {
            // Raw/blank layout
            echo $content;
        }
    }
}
