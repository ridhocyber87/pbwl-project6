<?php

namespace App\Core;

class Bootstrap
{
    public function __construct()
    {
        // Ambil URL dari REQUEST_URI jika tidak ada parameter 'page'
        $url = $_GET['page'] ?? $this->getRequestUri();

        // Parameter page harus berupa teks biasa
        if (!is_string($url)) {
            $this->tidakDitemukan();
            return;
        }

        // Filter dan bersihkan URL
        $url = trim((string) filter_var($url, FILTER_SANITIZE_URL), '/');
        $url = $url === '' ? [] : explode('/', $url);

        // Bagian pertama adalah nama controller, tanpa URL dipakai controller Index
        $page = $url ? ucfirst(strtolower(array_shift($url))) : 'Index';

        // Nama controller hanya boleh huruf dan angka, dan file-nya harus ada
        if (
            !preg_match('/^[A-Za-z][A-Za-z0-9]*$/', $page)
            || !file_exists(ROOT . "app/controllers/" . $page . ".php")
        ) {
            $this->tidakDitemukan();
            return;
        }

        $class = "App\\Controllers\\" . $page;
        $controller = new $class;

        // Bagian kedua adalah method, sisanya parameter: controller/method/params
        $action = $url ? array_shift($url) : 'index';
        $params = array_values($url);

        if (!$this->bisaDipanggil($controller, $action, $params)) {
            $this->tidakDitemukan();
            return;
        }

        call_user_func_array([$controller, $action], $params);
    }

    /**
     * Method hanya boleh dipanggil dari URL jika public, dibuat di controller itu sendiri
     * (bukan bawaan class Controller), dan jumlah parameternya sesuai
     */
    private function bisaDipanggil(object $controller, string $action, array $params): bool
    {
        if (!preg_match('/^[a-z][A-Za-z0-9]*$/', $action) || !method_exists($controller, $action)) {
            return false;
        }

        $method = new \ReflectionMethod($controller, $action);

        if (
            !$method->isPublic()
            || $method->isStatic()
            || $method->getDeclaringClass()->getName() !== get_class($controller)
        ) {
            return false;
        }

        return count($params) >= $method->getNumberOfRequiredParameters()
            && count($params) <= $method->getNumberOfParameters();
    }

    private function tidakDitemukan(): void
    {
        (new Error())->fileNotFound();
    }

    /**
     * Mendapatkan request URI (untuk Nginx atau Apache tanpa .htaccess)
     */
    private function getRequestUri(): string
    {
        // Hilangkan query string (misal: ?param=val)
        $uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH) ?: '';

        // Hilangkan nama folder aplikasi (misal: /project5) dari awal URI
        $folder = rtrim(str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'])), '/');

        if ($folder !== '' && str_starts_with($uri, $folder)) {
            $uri = substr($uri, strlen($folder));
        }

        // Hilangkan leading dan trailing slash
        return trim($uri, '/');
    }
}
