<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;
use Illuminate\Support\Facades\Validator;
use App\Rules\Cnpj;
use App\Rules\Cpf;

class ProfileController extends Controller
{
    /**
     * Display the user's profile form.
     */
    public function edit(Request $request): View
    {
        return view('profile.edit', [
            'user' => $request->user(),
        ]);
    }

    /**
     * Update the user's profile information.
     */
    public function update(ProfileUpdateRequest $request)
{
    // Validação básica do form request
    $validated = $request->validated();

    // Validação condicional do documento
    $documentRule = $request->document_type === 'CNPJ' 
        ? new Cnpj 
        : new Cpf;

    $validator = Validator::make($request->all(), [
        'document_number' => ['required', $documentRule]
    ]);

    if ($validator->fails()) {
        return redirect()
            ->route('profile.edit')
            ->withErrors($validator)
            ->withInput();
    }

    // Formatação dos dados
    $mergedData = array_merge($validated, [
        'document_number' => preg_replace('/[^0-9]/', '', $request->document_number),
        'phone' => preg_replace('/[^0-9]/', '', $request->phone),
        'company_name' => $request->company_name,
        'document_type' => $request->document_type
    ]);

    // Atualização do usuário
    $user = $request->user();
    $user->fill($mergedData);

    // Reset de verificação de e-mail se necessário
    if ($user->isDirty('email')) {
        $user->email_verified_at = null;
    }

    $user->save();

    return redirect()
        ->route('profile.edit')
        ->with('status', 'profile-updated');
}

    /**
     * Delete the user's account.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect(route('login1'));
    }
}
