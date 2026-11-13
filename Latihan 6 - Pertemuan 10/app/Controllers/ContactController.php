<?php

namespace App\Controllers;

class ContactController extends BaseController
{
    public function index(): string
    {
        return view('frontend/contact', [
            'title' => 'Hubungi kami',
            'active' => 'contact',
        ]);
    }

    public function send()
    {
        $rules = [
            'name' => 'required|min_length[3]|max_length[100]',
            'email' => 'required|valid_email|max_length[150]',
            'message' => 'required|min_length[10]|max_length[2000]',
        ];

        $messages = [
            'name' => [
                'required' => 'Nama perlu diisi.',
                'min_length' => 'Nama minimal terdiri dari 3 karakter.',
                'max_length' => 'Nama maksimal terdiri dari 100 karakter.',
            ],
            'email' => [
                'required' => 'Email perlu diisi.',
                'valid_email' => 'Masukkan alamat email dengan format yang benar.',
                'max_length' => 'Email maksimal terdiri dari 150 karakter.',
            ],
            'message' => [
                'required' => 'Pesan perlu diisi.',
                'min_length' => 'Pesan minimal terdiri dari 10 karakter.',
                'max_length' => 'Pesan maksimal terdiri dari 2.000 karakter.',
            ],
        ];

        if (! $this->validate($rules, $messages)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        return redirect()->to(url_to('contact'))
            ->with('success', 'Terima kasih, ' . $this->request->getPost('name') . '. Pesanmu sudah kami terima.');
    }
}
