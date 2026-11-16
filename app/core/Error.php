<?php

namespace App\Core;

class Error
{

	public function fileNotFound()
	{
		http_response_code(404);
		echo "<p>404 - Halaman tidak ditemukan</p>";
	}
}
