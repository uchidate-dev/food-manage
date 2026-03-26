@extends('layouts.app')

@section('content')

  @php
    // ユーザーごとのお気に入りID一覧（N+1回避）
    $favoriteIds = [];
    if (auth()->check()) {
        $favoriteIds = auth()->user()
            ->favoriteRecipes()
            ->pluck('recipes.id')
            ->toArray();
    }
  @endphp

  <div class="bg-emerald-50 text-stone-800 min-h-screen">
    <div class="max-w-6xl mx-auto px-6 py-8">

      {{-- ★ 左上：Homeへ戻る --}}
      <div class="mb-4">
        <a href="{{ url('/home') }}"
          class="inline-flex items-center gap-2 font-extrabold text-stone-700 no-underline hover:no-underline text-lg">
          <span class="text-xl">←</span>
          Homeへ戻る
        </a>
      </div>

      <div class="flex items-end justify-between gap-4">
        <div>
          <h1 class="text-4xl font-extrabold tracking-wide">レシピ一覧</h1>
        </div>

        <div class="flex items-center gap-3">
          <a href="{{ url('/recipe_register') }}"
            class="inline-flex items-center rounded-lg bg-stone-800 text-white px-4 py-2 text-sm font-bold hover:bg-stone-700">
            ＋ 新規作成
          </a>
        </div>
      </div>

      <div class="mt-8 grid grid-cols-12 gap-6">

        {{-- 左：フィルタ --}}
        <aside class="col-span-12 md:col-span-3">
          <div class="rounded-xl border border-stone-300 bg-white p-4">

            <div class="text-sm font-extrabold text-stone-600 mb-3">フィルタ</div>

            <div class="flex gap-2">
              <a href="{{ route('recipe.list', array_merge(request()->query(), ['scope'=>'all','page'=>null])) }}"
                class="flex-1 text-center rounded-lg px-3 py-2 text-sm font-bold border
                {{ $scope === 'all' ? 'bg-stone-800 text-white border-stone-800' : 'bg-white text-stone-800 border-stone-300 hover:bg-stone-50' }}">
                ALL
              </a>

              <a href="{{ route('recipe.list', array_merge(request()->query(), ['scope'=>'mine','page'=>null])) }}"
                class="flex-1 text-center rounded-lg px-3 py-2 text-sm font-bold border
                {{ $scope === 'mine' ? 'bg-stone-800 text-white border-stone-800' : 'bg-white text-stone-800 border-stone-300 hover:bg-stone-50' }}">
                マイレシピ
              </a>
            </div>

            <div class="mt-4">
              <a href="{{ route('recipe.list', array_merge(request()->query(), ['fav'=>$fav?0:1,'page'=>null])) }}"
                class="inline-flex items-center gap-2 rounded-lg border px-3 py-2 text-sm font-bold
                {{ $fav ? 'bg-pink-50 border-pink-200 text-pink-700' : 'bg-white border-stone-300 text-stone-700 hover:bg-stone-50' }}">
                <span class="text-lg">{{ $fav ? '♥' : '♡' }}</span>
                お気に入り
              </a>
            </div>

            <div class="mt-5">
              <div class="text-sm font-extrabold text-stone-600 mb-2">カテゴリ</div>

              <div class="space-y-1">
                <a href="{{ route('recipe.list', array_merge(request()->query(), ['cat'=>null,'page'=>null])) }}"
                  class="block rounded-lg px-3 py-2 text-sm font-bold
                  {{ empty($cat) ? 'bg-amber-100 text-stone-800' : 'hover:bg-stone-50' }}">
                  すべて
                </a>

                @foreach($categories as $c)
                  <a href="{{ route('recipe.list', array_merge(request()->query(), ['cat'=>$c->id,'page'=>null])) }}"
                    class="block rounded-lg px-3 py-2 text-sm font-bold
                    {{ (string)$cat === (string)$c->id ? 'bg-amber-100 text-stone-800' : 'hover:bg-stone-50' }}">
                    {{ $c->name }}
                  </a>
                @endforeach
              </div>
            </div>

            {{-- 検索 --}}
            <div class="mt-12">
              <form method="GET" action="{{ route('recipe.list') }}" class="space-y-2">

                <input type="hidden" name="scope" value="{{ $scope }}">
                <input type="hidden" name="cat" value="{{ $cat }}">
                <input type="hidden" name="fav" value="{{ $fav?1:0 }}">

                <input name="q"
                      value="{{ $q }}"
                      placeholder="タイトル・材料"
                      class="w-full rounded-lg border border-stone-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-stone-400">

                <button type="submit"
                        class="w-full rounded-lg bg-stone-800 text-white px-3 py-2 text-sm font-bold hover:bg-stone-700">
                  検索する
                </button>

                @if($q !== '')
                  <a href="{{ route('recipe.list', array_merge(request()->query(), ['q'=>null,'page'=>null])) }}"
                    class="block text-center rounded-lg border border-stone-300 px-3 py-2 text-sm font-bold hover:bg-stone-50">
                    検索解除
                  </a>
                @endif

              </form>
            </div>

            {{-- Newレシピ --}}
            <div class="mt-10 pt-6 border-t border-stone-200">

              <div class="mb-2 flex justify-center">
                <div class="inline-flex items-center gap-2 rounded-full bg-gradient-to-r from-pink-100 via-amber-100 to-emerald-100 px-3 py-1 text-sm font-extrabold text-stone-700 shadow-sm">
                  <span class="text-pink-500">✨</span>
                  <span class="tracking-wide">Newレシピ</span>
                  <span class="text-amber-500">✨</span>
                </div>
              </div>

              @if($latestRecipes->isEmpty())
                <div class="text-xs font-bold text-stone-500 text-center mt-3">
                  新しいレシピはまだありません。
                </div>
              @else

                <ul class="space-y-2 mt-6">
                  @foreach($latestRecipes as $r)

                    <li class="text-xs font-bold text-stone-600">

                      <a href="{{ route('recipe.detail',['id'=>$r->id,'back'=>url()->full()]) }}"
                        class="hover:underline block">
                        {{ $r->title }}
                      </a>

                      <div class="text-[10px] font-bold text-stone-400 mt-1 pl-3">
                        作成者：{{ optional($r->user)->name ?? '不明' }}
                      </div>

                    </li>

                  @endforeach
                </ul>

              @endif

            </div>

          </div>
        </aside>

        {{-- 右：一覧 --}}
        <main class="col-span-12 md:col-span-9">

          <div class="rounded-xl border border-stone-300 bg-white">

            <div class="px-5 py-4 border-b border-stone-200 flex items-center justify-between">
              <div class="text-sm font-extrabold text-stone-600">
                表示中：{{ $recipes->total() }}件（1ページ {{ $recipes->perPage() }}件）
              </div>
            </div>

            @if($recipes->count() === 0)

              <div class="p-8 text-stone-500 font-bold">
                条件に合うレシピがありません。
              </div>

            @endif

            <div class="divide-y divide-stone-200">

              @foreach($recipes as $recipe)

                @php
                  $isFavorited = in_array($recipe->id,$favoriteIds,true);
                @endphp

                <div class="px-5 py-4 flex items-center justify-between gap-6">

                  <div class="min-w-0">

                    <a href="{{ route('recipe.detail',['id'=>$recipe->id,'back'=>url()->full()]) }}"
                      class="block text-2xl font-extrabold truncate hover:underline">
                      {{ $recipe->title }}
                    </a>

                    <div class="mt-2 pl-10 flex flex-wrap items-center gap-x-4 gap-y-1 text-sm text-stone-500 font-bold">

                      <span>
                        作成者：
                        {{ optional($recipe->user)->name ?? '不明' }}
                      </span>

                      @if($recipe->private_flg == 1)
                        <span class="text-red-600">非公開</span>
                      @else
                        <span class="text-emerald-700">公開</span>
                      @endif

                    </div>

                  </div>

                  <div class="flex items-center gap-3 shrink-0">

                    <button
                        type="button"
                        class="favorite-btn text-2xl leading-none {{ $isFavorited ? 'text-pink-600' : 'text-stone-400' }}"
                        data-id="{{ $recipe->id }}">
                        {{ $isFavorited ? '♥' : '♡' }}
                    </button>

                    <a href="{{ route('recipe.detail',['id'=>$recipe->id,'back'=>url()->full()]) }}"
                      class="rounded-lg border border-stone-300 px-3 py-2 text-sm font-bold hover:bg-stone-50">
                      詳細
                    </a>

                  </div>

                </div>

              @endforeach

            </div>

            <div class="px-5 py-4 border-t border-stone-200">
              {{ $recipes->links() }}
            </div>

          </div>

        </main>

      </div>

    </div>
  </div>

@endsection

@section('js')
  <script src="{{ asset('js/recipe_favorite.js') }}"></script>
@endsection