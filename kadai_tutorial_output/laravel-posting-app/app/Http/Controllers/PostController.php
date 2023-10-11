<?php   //このファイルはコントローラのファイル。
namespace App\Http\Controllers;   // ← namespace（名前空間）はクラスの住所を示す

use Illuminate\Http\Request;
/* ↑ use宣言=このファイルではこのクラスを使います」と宣言
  このファイルではIlluminate\Httpフォルダの中にあるRequestクラスを使うよ.と宣言（正確なURLは↓）
  C:\xampp\htdocs\laravel-posting-app\vendor\laravel\framework\src\Illuminate\Http\Request.php
  宣言により、そのファイル内ではRequestと記述するだけでRequestクラスを呼び出せるようになる*/
   
  // やりとりするモデルを宣言。storeアクションでデータベース（postsテーブル）とやりとりするためにPostモデルを使うので、use宣言しておく
use App\Models\Post;
class PostController extends Controller {
   // 一覧(投稿一覧)ページ
   public function index() {   
      $posts = Post::latest()->get();  // ← postsテーブルの全データを新しい順で取得
      
      return view('posts.index', compact('posts'));
   } 
      /* ↑☆view()ヘルパー=ビューを表示するためのもの。
   　　  view('posts.index')のように、表示したいビューを引数として指定する
   　　  postsフォルダのindex.blade.phpファイル（ビュー画面のファイル）
   　　  resources/views/posts/index.blade.phpというURLだが、
   　　  resources/viewsを省略し、フォルダ名.ファイル名（.blade.phpは不要）と記述
   　　　
   　　　☆ ↑ all()やget()メソッドは、collectionというクラスのインスタンスを戻り値として返す
　　　　　collectionクラスは簡単にいえば配列をより使いやすくしたクラスなので、配列と同様にforeach文で中身を順番に取り出すことができる
         変数($posts)をビューに渡すには、view()ヘルパーの第2引数にPHPのcompact()関数を指定する方法が一般的。
         compact()関数＝引数に渡された変数とその値から配列を作成し、戻り値として返す関数
         compact()関数の引数にはビューに渡す変数名を文字列で指定、先頭の$（ドル記号）は不要なので注意。*/


   // 作成（新規投稿）ページ
   public function create() {
      return view('posts.create');
   }
    /* ↑ 表示するビューに、resources/views/posts/create.blade.phpを指定
         resources/viewsを省略し、フォルダ名.ファイル名（.blade.phpは不要）と記述*/

      // 作成機能
   public function store(Request $request) {       // ← 4行目くらいのuse宣言にあるRequestクラスを型として宣言
      $request->validate([
         'title' => 'required',
         'content' => 'required',
      ]);
      // ↑validate()メソッド = テキストボックスに書かれたtitle.contentにrequired（入力必須）というルールを設定
      // validate= 検証という意味。いわゆるチェック機能。今回は入力がなされているかをチェックしている。
      // チェックの仕方は多くあり、詳細は教材。  
      
      $post = new Post();                          // ← Postモデルをインスタンス化
      $post->title = $request->input('title');     // ← 「$post->title」はPostモデルとつながるPostsテーブル（データベース）のtitleカラムに投稿一覧のテキストボックスに書いたメッセージを代入
      $post->content = $request->input('content'); // ← 「$post->content」はPostモデルとつながるPostsテーブル（データベース）のcontentカラムに投稿一覧のテキストボックスに書いたメッセージを代入
      $post->save(); // ← postsテーブルに↑で取得・代入したデータを保存する必要があるらしい

      return redirect()->route('posts.index')->with('flash_message', '投稿が完了しました。');
      /*redirect()ヘルパー = Laravelでリダイレクトさせるためのヘルパー。リダイレクト先の指定方法は複数
      ある（教材に詳細）が、route()メソッドを使う方法が見やすくURLの変更にも影響を受けないのでおすすめ。
      route()メソッド＝名前付きルートを指定するメソッド 「return redirect()->route('ルート名');」って記述。
      with()メソッド=フラッシュメッセージ（「投稿が完了しました」処理結果をユーザーに伝えるメッセ）を表示させるメソッド
      　　　　　　　  第1引数にキー、第2引数に値を指定することで、セッション(データ保存機能）にそのデータを保存できる。
                     セッションに保存されたデータはsession()ヘルパーを使えば取得でき、別ファイル（ビュー）で呼び出すように表示できる。
                     例えばビュー内で{{ session('flash_message') }}と記述し、flash_messageというキーの値を表示できる。
      */     
   }
   // 詳細ページ
   public function show(Post $post) {
      return view('posts.show', compact('post'));  
  }

  /* ↑ 表示するビューに、resources/views/posts/show.blade.phpを指定(blade.phpは省略されている)
         resources/viewsを省略し、フォルダ名.ファイル名（.blade.phpは不要）と記述
   ↑ compact('post')により、postsテーブルの全データを取得.'post'は14行目くらいに書いた$postを指す
     compact()関数＝引数に渡された変数とその値から配列を作成し、戻り値として返す関数
     compact()関数の引数にはビューに渡す変数名を文字列で指定、先頭の$（ドル記号）は不要なので注意。
*/
   // 更新ページ
   public function edit(Post $post) {
      return view('posts.edit', compact('post'));
   }

   // 更新機能　↓validate()メソッド = テキストボックスに書かれたtitle.contentにrequired（入力必須）というルールを設定
   public function update(Request $request, Post $post) {
      $request->validate([
         'title' => 'required',
         'content' => 'required',
      ]);
      $post->title = $request->input('title');
      $post->content = $request->input('content');
      $post->save();

      return redirect()->route('posts.show', $post)->with('flash_message', '投稿を編集しました。');
   }

   // 削除機能
   public function destroy(Post $post) {
      $post->delete();

      return redirect()->route('posts.index')->with('flash_message', '投稿を削除しました。');
   }
}

