<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Usuario;

class ControllerUsuarios extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //Pagina de inicio de sesion
        return view('inicioS.sesion');
    }

    public function login(Request $request){
        try{
            $datos = [
                'username' => $request->username,
                'password' => $request->password,
            ];
            // Esto va a buscar el username y password que se ingresaron, el primero que encuentre, por eso poner valores diferentes mijos
            $usuario = Usuario::where('username', $datos['username'])->where('password', $datos['password'])->first();
            // Verifica que el usuario no es nulo
            if($usuario){
                //Establecemos datos de sesion
                session([
                    'username' => $usuario->username,
                    'tipo' => $usuario->tipo]
                );
                return redirect('/')->with('mensaje', 'Inicio de sesión exitoso.');
            }else{
                return redirect()->back()->with('error', 'Credenciales incorrectas');
            }
                
        }
        catch (\Exception $e){
            return redirect()->back()->with('error', 'Error de credenciales: ' . $e->getMessage());
        }
    }

    public function logout(Request $request){
        //Elimina todo de la sesion
        session()->flush();
        return redirect('/')->with('mensaje', 'Sesión cerrada correctamente.');
    }

    public function lista(){
        if (session('tipo') !== 'Administrador') {
            return redirect('/')->with('error', 'Acceso no autorizado.');
        }
        //Lista de los usuarios
        $usuarios = Usuario::all();
        return view('inicioS.inicio', compact('usuarios'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //Vista para crear cuenta
        return view('inicioS.registro');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //Guardar en la bdd
        try{
            $datos = [
                'username' => $request->username,
                'email' => $request->email,
                'password' => $request->password,
                'tipo' => $request->input('tipo', 'Visitante'),
            ];
            Usuario::create($datos);
            if(session('tipo') == 'Administrador'){
                return redirect()->route('inicioS.lista')->with('mensaje', 'Usuario registrado correctamente.');
            }else{
                return redirect()->route('inicioS.index')->with('mensaje', 'Registro exitoso.');
            }
        }
        catch (\Exception $e){
            return redirect()->back()->with('error', 'Hubo un error al registrarse: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        if (session('tipo') !== 'Administrador') {
            return redirect('/')->with('error', 'Acceso no autorizado.');
        }
        //Vista para editar
        $usuario = Usuario::findOrFail($id);
        return view('inicioS.editar', compact('usuario'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        if (session('tipo') !== 'Administrador') {
            return redirect('/')->with('error', 'Acceso no autorizado.');
        }
        //Actualizar los datos que mandamos para editar
        try{
            $usuario = Usuario::findOrFail($id);
            $usuario->update([
                'username' => $request->username,
                'email' => $request->email,
                'password' => $request->password,
                'tipo' => $request->tipo
            ]);
            return redirect()->route('inicioS.lista')->with('mensaje', 'Usuario actualizado correctamente.');
        }
        catch (\Exception $e){
            return redirect()->back()->with('error', 'Hubo un error al actualizar el usuario: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        if (session('tipo') !== 'Administrador') {
            return redirect('/')->with('error', 'Acceso no autorizado.');
        }
        //Eliminar
        try{
            $usuario = Usuario::findOrFail($id);
            $usuario->delete();

            return redirect()->route('inicioS.lista')->with('mensaje', 'Usuario eliminado correctamente.');
        }
        catch (\Exception $e){
            return redirect()->back()->with('error', 'Hubo un error al eliminar el usuario: ' . $e->getMessage());
        }
    }
}
