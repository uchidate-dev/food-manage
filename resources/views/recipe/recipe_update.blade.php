@extends('layouts.app')

@section('css')
    <link rel="stylesheet" href="{{ asset('css/recipe_update.css') }}">
@endsection

@section('content')
<div class="page-header">
    <a href="/recipe_list" class="back-btn"><i class="bi bi-chevron-left"></i></a>
    <h1>レシピ編集</h1>
</div>

<div class="container">

    @if ($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="/recipe_update/{{ $recipe->id }}">
        @csrf
        @method('PATCH')

        <div class="row">
            {{-- 左カラム --}}
            <div class="col-12 col-md-5">

                {{-- 料理完成画像 --}}
                <div class="mb-3">
                    <div class="position-relative" style="width:100%;">
                        <div class="border rounded d-flex flex-column align-items-center justify-content-center bg-light"
                            style="height:180px; cursor:pointer; overflow:hidden;"
                            onclick="document.getElementById('finished_image').click()">

                            {{-- 既存画像がある場合はプレビュー表示 --}}
                            @if($recipe->finished_image)
                                <img id="finished_preview" src="{{ $recipe->finished_image }}" alt="" style="width:100%; height:100%; object-fit:cover; display:block;">
                                <div id="finished_placeholder" class="text-center" style="display:none;">
                                    <i class="bi bi-camera fs-2 text-secondary"></i>
                                    <span class="text-secondary small mt-1 d-block">＋料理写真をアップロード</span>
                                </div>
                            @else
                                <img id="finished_preview" src="" alt="" style="width:100%; height:100%; object-fit:cover; display:none;">
                                <div id="finished_placeholder" class="text-center">
                                    <i class="bi bi-camera fs-2 text-secondary"></i>
                                    <span class="text-secondary small mt-1 d-block">＋料理写真をアップロード</span>
                                </div>
                            @endif
                        </div>
                        {{-- ×ボタン（画像の右上に重ねて表示） --}}
                        <button type="button"
                            id="clear_finished_image_btn"
                            onclick="clearFinishedImage()"
                            style="position:absolute; top:-6px; right:-6px; width:20px; height:20px; border-radius:50%; background:#dc3545; color:white; border:none; font-size:12px; line-height:1; padding:0; {{ $recipe->finished_image ? '' : 'display:none;' }}">
                            ×
                        </button>
                    </div>
                    <input type="file" id="finished_image" accept="image/*" class="d-none">
                    <input type="hidden" name="finished_image" id="finished_image_base64" value="{{ $recipe->finished_image }}">
                </div>

                {{-- レシピ名 --}}
                <div class="mb-3">
                    <label class="form-label fw-bold">レシピ名</label>
                    <input type="text" name="title" class="form-control" value="{{ $recipe->title }}" placeholder="例：オムライス">
                </div>

                {{-- 参考レシピURL --}}
                <div class="mb-3">
                    <label class="form-label fw-bold">参考レシピURL</label>
                    <input type="url" name="url" class="form-control" value="{{ $recipe->url }}" placeholder="例：https://〜">
                </div>

                {{-- 料理の説明 --}}
                <div class="mb-3">
                    <label class="form-label fw-bold">料理の説明</label>
                    <textarea name="memo" class="form-control" rows="3" placeholder="例：簡単な説明">{{ $recipe->memo }}</textarea>
                </div>

                {{-- カテゴリ --}}
                <div class="mb-3">
                    <label class="form-label fw-bold">カテゴリ</label>
                    <select name="recipe_category_id" class="form-select" style="width:auto;">
                        <option value="">未選択</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}"
                                {{ $recipe->recipe_category_id == $category->id ? 'selected' : '' }}>
                                {{ $category->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- 材料 --}}
                <div class="mb-3">
                    <label class="form-label fw-bold">材料</label>
                    <textarea name="ingredients" class="form-control" rows="5"
                        placeholder="例：1人前&#13;&#10;卵　1個&#13;&#10;ごはん　100g…">{{ $recipe->ingredients }}</textarea>
                </div>

            </div>

            {{-- 右カラム --}}
            <div class="col-12 col-md-7">

                {{-- 登録日・最終更新日 --}}
                <div class="text-end mb-2 text-secondary small">
                    登録日：{{ $recipe->created_at->format('Y/m/d') }} / 最終更新日：{{ $recipe->updated_at->format('Y/m/d') }}
                </div>

                <p class="fw-bold fs-5 text-center">作り方</p>

                <div id="steps-container">
                    @for($i = 1; $i <= 15; $i++)
                        @if($recipe->{"step{$i}"} || $recipe->{"step_image{$i}"})
                        <div class="step-item d-flex align-items-start mb-3">
                            <span class="fw-bold me-2 mt-1" style="white-space:nowrap;">手順{{ $i }}</span>

                            <div class="position-relative me-2 flex-shrink-0" style="width:80px; height:80px;">
                                <div class="border rounded bg-light d-flex align-items-center justify-content-center"
                                    style="width:80px; height:80px; cursor:pointer; overflow:hidden;"
                                    onclick="document.getElementById('step_image_file{{ $i }}').click()">
                                    @if($recipe->{"step_image{$i}"})
                                        <img id="step_preview_{{ $i }}" src="{{ $recipe->{'step_image'.$i} }}" alt="" style="width:100%; height:100%; object-fit:cover; display:block;">
                                        <i id="step_icon_{{ $i }}" class="bi bi-camera text-secondary" style="display:none;"></i>
                                    @else
                                        <img id="step_preview_{{ $i }}" src="" alt="" style="width:100%; height:100%; object-fit:cover; display:none;">
                                        <i id="step_icon_{{ $i }}" class="bi bi-camera text-secondary"></i>
                                    @endif
                                </div>
                                <button type="button"
                                    id="clear_step_btn_{{ $i }}"
                                    onclick="clearStepImage({{ $i }})"
                                    style="position:absolute; top:-6px; right:-6px; width:20px; height:20px; border-radius:50%; background:#dc3545; color:white; border:none; font-size:12px; line-height:1; padding:0; {{ $recipe->{'step_image'.$i} ? '' : 'display:none;' }}">
                                    ×
                                </button>
                            </div>

                            <input type="file" id="step_image_file{{ $i }}" accept="image/*" class="d-none">
                            <input type="hidden" name="step_image{{ $i }}" id="step_image_base64_{{ $i }}" value="{{ $recipe->{'step_image'.$i} }}">
                            <textarea name="step{{ $i }}" class="form-control me-2" rows="3" placeholder="例：ごはんを炒めます">{{ $recipe->{"step{$i}"} }}</textarea>
                            <button type="button" class="btn btn-secondary btn-sm rounded-circle delete-step-btn">
                                <i class="bi bi-x"></i>
                            </button>
                        </div>
                        @endif
                    @endfor
                </div>

                {{-- ＋手順ボタン --}}
                <div class="text-center mb-3">
                    <button type="button" id="add-step-btn" class="btn btn-link text-decoration-none fw-bold">＋手順</button>
                </div>

            </div>
        </div>

        {{-- 公開設定・ボタン --}}
        <div class="text-center mt-2 mb-2">
            <div class="mb-3">
                <input type="checkbox" id="private_flg" name="private_flg" value="1"
                    {{ $recipe->private_flg == 0 ? 'checked' : '' }}>
                <label for="private_flg">レシピを公開する</label>
            </div>
            <button type="submit" class="btn btn-primary px-5 me-3">更新する</button>
            <a href="/recipe_list" class="text-secondary text-decoration-none">キャンセル</a>
        </div>

    </form>

    {{-- レシピを削除 --}}
    <div class="mt-2 mb-4">
        <form method="POST" action="/recipe_update/{{ $recipe->id }}">
            @csrf
            @method('DELETE')
            <button type="submit" class="btn btn-link text-danger text-decoration-none">
                <i class="bi bi-trash"></i> レシピを削除
            </button>
        </form>
    </div>

</div>
@endsection

@section('js')
<script>
    let stepCount = {{ collect(range(1, 15))->filter(fn($i) => $recipe->{"step{$i}"} || $recipe->{"step_image{$i}"})->count() }};
</script>
    <script src="{{ asset('js/recipe_update.js') }}"></script>
@endsection