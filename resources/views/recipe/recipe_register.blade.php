@extends('layouts.app')

@section('css')
    <link rel="stylesheet" href="{{ asset('css/recipe_register.css') }}">
@endsection

@section('content')
<div class="page-header">
    <a href="/recipe_list" class="back-btn"><i class="bi bi-chevron-left"></i></a>
    <h1>レシピ登録</h1>
</div>

<div class="container">
    <form method="POST" action="/recipe_register">
        @if ($errors->any())
            <div class="alert alert-danger">
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
        @csrf
        <div class="row">
            {{-- 左カラム --}}
            <div class="col-12 col-md-5">

                {{-- 料理完成画像 --}}
                <div class="mb-3">
                    <div class="position-relative" style="width:100%;">
                        <div class="border rounded d-flex flex-column align-items-center justify-content-center bg-light"
                            style="height:180px; cursor:pointer; overflow:hidden;"
                            onclick="document.getElementById('finished_image').click()">
                            <img id="finished_preview" src="" alt="" style="width:100%; height:100%; object-fit:cover; display:none;">
                            <div id="finished_placeholder" class="text-center">
                                <i class="bi bi-camera fs-2 text-secondary"></i>
                                <span class="text-secondary small mt-1 d-block">＋料理写真をアップロード</span>
                            </div>
                        </div>
                        {{-- ×ボタン（画像の右上に重ねて表示） --}}
                        <button type="button"
                            id="clear_finished_image_btn"
                            onclick="clearFinishedImage()"
                            style="position:absolute; top:-6px; right:-6px; width:20px; height:20px; border-radius:50%; background:#dc3545; color:white; border:none; font-size:12px; line-height:1; padding:0; display:none;">
                            ×
                        </button>
                    </div>
                    <input type="file" id="finished_image" accept="image/*" class="d-none">
                    <input type="hidden" name="finished_image" id="finished_image_base64">
                </div>

                {{-- レシピ名 --}}
                <div class="mb-3">
                    <label class="form-label fw-bold">レシピ名</label>
                    <input type="text" name="title" class="form-control" placeholder="例：オムライス">
                </div>

                {{-- 参考レシピURL --}}
                <div class="mb-3">
                    <label class="form-label fw-bold">参考レシピURL</label>
                    <input type="url" name="url" class="form-control" placeholder="例：https://〜">
                </div>

                {{-- 料理の説明 --}}
                <div class="mb-3">
                    <label class="form-label fw-bold">料理の説明</label>
                    <textarea name="memo" class="form-control" rows="3" placeholder="例：簡単な説明"></textarea>
                </div>

                {{-- カテゴリ --}}
                <div class="mb-3">
                    <label class="form-label fw-bold">カテゴリ</label>
                    <select name="recipe_category_id" class="form-select" style="width:auto;">
                        <option value="">未選択</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}">{{ $category->name }}</option>
                        @endforeach
                    </select>
                </div>

                {{-- 材料 --}}
                <div class="mb-3">
                    <label class="form-label fw-bold">材料</label>
                    <textarea name="ingredients" class="form-control" rows="5"
                        placeholder="例：1人前&#13;&#10;卵　1個&#13;&#10;ごはん　100g…"></textarea>
                </div>

            </div>

            {{-- 右カラム --}}
            <div class="col-12 col-md-7">
                <p class="fw-bold fs-5 text-center">作り方</p>

                <div id="steps-container">

                    {{-- 手順1 --}}
                    <div class="step-item d-flex align-items-start mb-3">
                        <span class="fw-bold me-2 mt-1" style="white-space:nowrap;">手順1</span>
                        <div class="position-relative me-2 flex-shrink-0" style="width:80px; height:80px;">
                            <div class="border rounded bg-light d-flex align-items-center justify-content-center"
                                style="width:80px; height:80px; cursor:pointer; overflow:hidden;"
                                onclick="document.getElementById('step_image_file1').click()">
                                <img id="step_preview_1" src="" alt="" style="width:100%; height:100%; object-fit:cover; display:none;">
                                <i id="step_icon_1" class="bi bi-camera text-secondary"></i>
                            </div>
                            {{-- ×ボタン（画像の右上に重ねて表示） --}}
                            <button type="button"
                                id="clear_step_btn_1"
                                onclick="clearStepImage(1)"
                                style="position:absolute; top:-6px; right:-6px; width:20px; height:20px; border-radius:50%; background:#dc3545; color:white; border:none; font-size:12px; line-height:1; padding:0; display:none;">
                                ×
                            </button>
                        </div>
                        <input type="file" id="step_image_file1" accept="image/*" class="d-none">
                        <input type="hidden" name="step_image1" id="step_image_base64_1">
                        <textarea name="step1" class="form-control me-2" rows="3" placeholder="例：ごはんを炒めます"></textarea>
                        <button type="button" class="btn btn-secondary btn-sm rounded-circle delete-step-btn">
                            <i class="bi bi-x"></i>
                        </button>
                    </div>

                    {{-- 手順2 --}}
                    <div class="step-item d-flex align-items-start mb-3">
                        <span class="fw-bold me-2 mt-1" style="white-space:nowrap;">手順2</span>
                        <div class="position-relative me-2 flex-shrink-0" style="width:80px; height:80px;">
                            <div class="border rounded bg-light d-flex align-items-center justify-content-center"
                                style="width:80px; height:80px; cursor:pointer; overflow:hidden;"
                                onclick="document.getElementById('step_image_file2').click()">
                                <img id="step_preview_2" src="" alt="" style="width:100%; height:100%; object-fit:cover; display:none;">
                                <i id="step_icon_2" class="bi bi-camera text-secondary"></i>
                            </div>
                            {{-- ×ボタン（画像の右上に重ねて表示） --}}
                            <button type="button"
                                id="clear_step_btn_2"
                                onclick="clearStepImage(2)"
                                style="position:absolute; top:-6px; right:-6px; width:20px; height:20px; border-radius:50%; background:#dc3545; color:white; border:none; font-size:12px; line-height:1; padding:0; display:none;">
                                ×
                            </button>
                        </div>
                        <input type="file" id="step_image_file2" accept="image/*" class="d-none">
                        <input type="hidden" name="step_image2" id="step_image_base64_2">
                        <textarea name="step2" class="form-control me-2" rows="3" placeholder="例：ごはんを炒めます"></textarea>
                        <button type="button" class="btn btn-secondary btn-sm rounded-circle delete-step-btn">
                            <i class="bi bi-x"></i>
                        </button>
                    </div>

                    {{-- 手順3 --}}
                    <div class="step-item d-flex align-items-start mb-3">
                        <span class="fw-bold me-2 mt-1" style="white-space:nowrap;">手順3</span>
                        <div class="position-relative me-2 flex-shrink-0" style="width:80px; height:80px;">
                            <div class="border rounded bg-light d-flex align-items-center justify-content-center"
                                style="width:80px; height:80px; cursor:pointer; overflow:hidden;"
                                onclick="document.getElementById('step_image_file3').click()">
                                <img id="step_preview_3" src="" alt="" style="width:100%; height:100%; object-fit:cover; display:none;">
                                <i id="step_icon_3" class="bi bi-camera text-secondary"></i>
                            </div>
                            {{-- ×ボタン（画像の右上に重ねて表示） --}}
                            <button type="button"
                                id="clear_step_btn_3"
                                onclick="clearStepImage(3)"
                                style="position:absolute; top:-6px; right:-6px; width:20px; height:20px; border-radius:50%; background:#dc3545; color:white; border:none; font-size:12px; line-height:1; padding:0; display:none;">
                                ×
                            </button>
                        </div>
                        <input type="file" id="step_image_file3" accept="image/*" class="d-none">
                        <input type="hidden" name="step_image3" id="step_image_base64_3">
                        <textarea name="step3" class="form-control me-2" rows="3" placeholder="例：ごはんを炒めます"></textarea>
                        <button type="button" class="btn btn-secondary btn-sm rounded-circle delete-step-btn">
                            <i class="bi bi-x"></i>
                        </button>
                    </div>
                </div>

                {{-- ＋手順ボタン --}}
                <div class="text-center mb-3">
                    <button type="button" id="add-step-btn" class="btn btn-link text-decoration-none fw-bold">＋手順</button>
                </div>

            </div>
        </div>

        {{-- 公開設定・ボタン --}}
        <div class="text-center mt-2 mb-4">
            <div class="mb-3">
                <input type="checkbox" id="private_flg" name="private_flg" value="1">
                <label for="private_flg">レシピを公開する</label>
            </div>
            <button type="submit" class="btn btn-success px-5 me-3">レシピを登録する</button>
            <a href="/recipe_list" class="text-secondary text-decoration-none">キャンセル</a>
        </div>

    </form>
</div>
@endsection

@section('js')
    <script src="{{ asset('js/recipe_register.js') }}"></script>
@endsection