<?php

namespace App\Filters;

use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\Filters\FilterInterface;

class Auth implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        // Cek apakah sudah login
        if (!session()->get('login')) {

            // Kalau request ke login atau assets, biarkan
            $uri = current_url();
            if (strpos($uri, '/login') !== false || strpos($uri, '/assets') !== false) {
                return;
            }

            // Cek apakah AJAX
            if ($request->isAJAX()) {
                return service('response')
                    ->setStatusCode(401)
                    ->setJSON([
                        'status' => false,
                        'message' => 'Silakan login terlebih dahulu.'
                    ]);
            }

            // Redirect normal
            return redirect()->to('/login')->with('error', 'Silakan login terlebih dahulu.');
        }
    }


    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // Tidak perlu aksi setelah response
    }
}