<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreUpdatePost;
use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class PostController extends Controller
{
    public function index()
    {
        $posts = Post::latest()->paginate(); //método pronto para paginação dos itens listados na página. Por default são 15 itens
        return view('admin.posts.index', compact('posts'));
    }

    public function create()
    {
        return view('admin.posts.create');
    }

    public function store(StoreUpdatePost $request) //criando o objeto de Resquest
    {
        $data = $request->all();
        //upload de arquivos
        if($request->image->isValid())
        {
            $nameFile = Str::of($request->title)->slug('-') . '.' .$request->image->getClientOriginalExtension(); //nome do arquivo da imagem

            $image = $request->image->storeAs('posts', $nameFile); //cria a pasta posts e vai incluindo as imagens
            $data['image'] = $image; //pegando a imagem
        }

        Post::create($data); //insere automaticamente todos os campos

        return redirect() 
            ->route('posts.index')
            ->with('message', 'Post criado com sucesso!');
    }

    public function show($id)
    {
        if (!$post = Post::find($id))
        {
            return redirect()->route('posts.index');
        } //vai encontrar o post com o respectivo ID no model e não deve estar vazio, caso esteja, vai ser direcionado para o index

        return view('admin.posts.show', compact('post'));
    }

    public function destroy($id)
    {
        if (!$post = Post::find($id))
        {
            return redirect()->route('posts.index');
        }

        if(Storage::exists($post->image))
        {
            Storage::delete(($post->image));
        }

        $post->delete;

        return redirect()
            ->route('posts.index')
            ->with('message', 'Post deletado com sucesso!'); //igual ViewBag, informando a mensagem para retornar na View
    }

    public function edit($id)
    {
        if (!$post = Post::find($id))
        {
            return redirect()->back();
        } 

        return view('admin.posts.edit', compact('post'));
    }

    public function update(StoreUpdatePost $request ,$id) //injeção de dependência para já validar o envio
    {

        if (!$post = Post::find($id))
        {
            return redirect()->back();
        }

        $data = $request->all();

        //upload de arquivos
        if($request->image && $request->image->isValid())
        {
            //verifica se já existe o arquivo, caso exista, é feito o delete
            if(Storage::exists($post->image))
            {
                Storage::delete(($post->image));
            }
            $nameFile = Str::of($request->title)->slug('-') . '.' .$request->image->getClientOriginalExtension(); //nome do arquivo da imagem

            $image = $request->image->storeAs('posts', $nameFile); //cria a pasta posts e vai incluindo as imagens
            $data['image'] = $image; //pegando a imagem
        }

        $post->update($data);

        return redirect()
            ->route('posts.index')
            ->with('message', 'Post atualizado com sucesso!');
    }  
    
    public function search(Request $request)
    {
        $filters = $request->except('_token'); //técnica para não bugar a paginação
        //método para filtrar
        $posts = Post::where('title', 'LIKE',"%{$request->search}%") //comparando exatamente igual =
                        ->orWhere('content', 'LIKE', "%{$request->search}%") //comparando resultado no começo e no fim
                        ->paginate(); //método para paginar
        
        return view('admin.posts.index', compact('posts'));
    }
    
}
