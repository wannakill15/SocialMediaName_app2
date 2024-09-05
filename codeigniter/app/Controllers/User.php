<?php
namespace App\Controllers;

use App\Models\UserModel;
use CodeIgniter\Controller;

class User extends Controller
{
    public function register()
    {
        return view('register'); //register is file name in App\View\register.php
    }

    //store data in database from post form
    public function store()
    {
        $userModel = new UserModel();
    
        $name = $this->request->getPost('name');
        $email = $this->request->getPost('email');
        $password = $this->request->getPost('password');
    
        if (empty($name) || empty($email) || empty($password)) {
            return redirect()->back()->with('error', 'All fields are required.');
        }
    
        if ($userModel->where('email', $email)->first()) {
            return redirect()->back()->with('error', 'Email address already in use.');
        }
    
        $data = [
            'name' => $name,
            'email' => $email,
            'password' => password_hash($password, PASSWORD_DEFAULT)
        ];
    
        $userModel->save($data);
    
        return redirect()->to('/login'); 
    }

    //login iu
    public function login()
    {
        return view('login');
    }

    //verify login
    public function verifylogin()
    {

        // Call model
        $userModel = new UserModel();

        // Get data from post form
        $name = $this->request->getPost('name');
        $password = $this->request->getPost('password');

        // Check if user exists in the database
        $data = $userModel->where('name', $name)->first();

        if ($data) {
            // Verify the provided password with the hashed password stored in the database
            if (password_verify($password, $data['password'])) {
                // Set session to store login success
                session()->set([
                    'id' => $data['id'],
                    'name' => $data['name'],
                    'logged_in' => true,
                ]);

                // Redirect to home page
                return redirect()->to('/');
            } else {
                // Password did not match, redirect to login with an error message
                return redirect()->to('/login')->with('error', 'Invalid login credentials.');
            }
        } else {
            // User not found, redirect to login with an error message
            return redirect()->to('/login')->with('error', 'Invalid login credentials.');
        }
    }

    //logout function
    public function logout()
    {
        //destroy all session
        session()->destroy();
        //redirect to login page
        return redirect()->to('/login');
    }

    public function profile()
    {
        // Redirect to login if accessing profile page without logging in
        if (!session()->get('logged_in')) {
            return redirect()->to('/login');
        }
    
        // Get the currently logged-in user's ID
        $userId = session()->get('id');
    
        // Load the user model
        $userModel = new UserModel();
    
        // Retrieve the user's data from the database
        $userData = $userModel->find($userId);
    
        // Pass the user data to the profile view
        return view('profile', ['user' => $userData]);
    }

    public function update()
    {
        // Get the currently logged-in user's ID
        $userId = session()->get('id');

        // Load the user model
        $userModel = new UserModel();

        // Get the updated data from the form
        $data = [
            'name' => $this->request->getPost('name'),
            'email' => $this->request->getPost('email'),
        ];

        // Check if a new password is provided
        $newPassword = $this->request->getPost('password');
        if (!empty($newPassword)) {
            // Hash the new password before saving it
            $data['password'] = password_hash($newPassword, PASSWORD_DEFAULT);
        }

        // Update the user's data in the database
        $userModel->update($userId, $data);

        // Redirect back to the profile page
        return redirect()->to('/profile');
    }
}