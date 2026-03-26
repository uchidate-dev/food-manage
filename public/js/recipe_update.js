    const maxSteps = 15;

    // ＋手順ボタン：手順を追加
    document.getElementById('add-step-btn').addEventListener('click', function () {
        if (stepCount >= maxSteps) {
            alert('手順は最大15件までです');
            return;
        }
        stepCount++;

        const stepItem = document.createElement('div');
        stepItem.classList.add('step-item', 'd-flex', 'align-items-start', 'mb-3');
        stepItem.innerHTML = `
            <span class="fw-bold me-2 mt-1" style="white-space:nowrap;">手順${stepCount}</span>
            
            <div class="position-relative me-2 flex-shrink-0" style="width:80px; height:80px;">
                <div class="border rounded bg-light d-flex align-items-center justify-content-center"
                    style="width:80px; height:80px; cursor:pointer; overflow:hidden;"
                    onclick="document.getElementById('step_image_file${stepCount}').click()">
                    <img id="step_preview_${stepCount}" src="" alt="" style="width:100%; height:100%; object-fit:cover; display:none;">
                    <i id="step_icon_${stepCount}" class="bi bi-camera text-secondary"></i>
                </div>
                <button type="button"
                    id="clear_step_btn_${stepCount}"
                    onclick="clearStepImage(${stepCount})"
                    style="position:absolute; top:-6px; right:-6px; width:20px; height:20px; border-radius:50%; background:#dc3545; color:white; border:none; font-size:12px; line-height:1; padding:0; display:none;">
                    ×
                </button>
            </div>

            <input type="file" id="step_image_file${stepCount}" accept="image/*" class="d-none">
            <input type="hidden" name="step_image${stepCount}" id="step_image_base64_${stepCount}">
            <textarea name="step${stepCount}" class="form-control me-2" rows="3" placeholder="例：ごはんを炒めます"></textarea>
            <button type="button" class="btn btn-secondary btn-sm rounded-circle delete-step-btn">
                <i class="bi bi-x"></i>
            </button>
        `;

        document.getElementById('steps-container').appendChild(stepItem);
        bindStepImageEvent(`step_image_file${stepCount}`, `step_image_base64_${stepCount}`, stepCount);
        updateDeleteButtons();
    });

    // 削除ボタン：手順を削除して番号を振り直す
    document.getElementById('steps-container').addEventListener('click', function (e) {
        const btn = e.target.closest('.delete-step-btn');
        if (!btn) return;

        const steps = document.querySelectorAll('.step-item');
        if (steps.length <= 1) {
            alert('手順は最低1件必要です');
            return;
        }

        btn.closest('.step-item').remove();
        stepCount--;
        renumberSteps();
        updateDeleteButtons();
    });

    // 手順番号とname属性・IDを振り直す
    function renumberSteps() {
        const steps = document.querySelectorAll('.step-item');
        steps.forEach((step, index) => {
            const num = index + 1;

            // ラベル
            step.querySelector('span').textContent = `手順${num}`;

            // textarea
            step.querySelector('textarea').name = `step${num}`;

            // 画像エリアのonclick
            const imgArea = step.querySelector('.border.rounded.bg-light');
            if (imgArea) {
                imgArea.setAttribute('onclick', `document.getElementById('step_image_file${num}').click()`);
            }

            // プレビュー画像
            const preview = step.querySelector('img');
            if (preview) preview.id = `step_preview_${num}`;

            // カメラアイコン
            const icon = step.querySelector('i.bi-camera');
            if (icon) icon.id = `step_icon_${num}`;

            // ×ボタン（画像削除）
            const clearBtn = step.querySelector('[id^="clear_step_btn_"]');
            if (clearBtn) {
                clearBtn.id = `clear_step_btn_${num}`;
                clearBtn.setAttribute('onclick', `clearStepImage(${num})`);
            }

            // file input
            const fileInput = step.querySelector('input[type="file"]');
            if (fileInput) fileInput.id = `step_image_file${num}`;

            // hidden input
            const hiddenInput = step.querySelector('input[type="hidden"]');
            if (hiddenInput) {
                hiddenInput.name = `step_image${num}`;
                hiddenInput.id = `step_image_base64_${num}`;
            }
        });
    }

    // 手順が1件のときは削除ボタンを無効化
    function updateDeleteButtons() {
        const steps = document.querySelectorAll('.step-item');
        const btns = document.querySelectorAll('.delete-step-btn');
        btns.forEach(btn => {
            btn.disabled = steps.length <= 1;
        });
    }
    updateDeleteButtons();
    
    // 画像削除ボタン：プレビューを非表示にしてhidden inputを空にする
    function clearStepImage(stepNum) {
        const preview = document.getElementById('step_preview_' + stepNum);
        const icon = document.getElementById('step_icon_' + stepNum);
        const hiddenInput = document.getElementById('step_image_base64_' + stepNum);
        const fileInput = document.getElementById('step_image_file' + stepNum);
        const clearBtn = document.getElementById('clear_step_btn_' + stepNum);

        if (preview) { preview.src = ''; preview.style.display = 'none'; }
        if (icon) { icon.style.display = 'block'; }
        if (hiddenInput) { hiddenInput.value = ''; }
        if (fileInput) { fileInput.value = ''; }
        if (clearBtn) { clearBtn.style.display = 'none'; }
    }

    // 料理完成画像を削除
    function clearFinishedImage() {
        const preview = document.getElementById('finished_preview');
        const placeholder = document.getElementById('finished_placeholder');
        const hiddenInput = document.getElementById('finished_image_base64');
        const fileInput = document.getElementById('finished_image');
        const clearBtn = document.getElementById('clear_finished_image_btn');

        if (preview) { preview.src = ''; preview.style.display = 'none'; }
        if (placeholder) { placeholder.style.display = 'block'; }
        if (hiddenInput) { hiddenInput.value = ''; }
        if (fileInput) { fileInput.value = ''; }
        if (clearBtn) { clearBtn.style.display = 'none'; }
    }

    // 料理完成画像をbase64変換＋プレビュー表示
    document.getElementById('finished_image').addEventListener('change', function () {
        const file = this.files?.[0];
        if (!file) return;

        const reader = new FileReader();
        reader.onload = function (e) {
            const dataUrl = e.target.result;

            // hidden inputに保存
            document.getElementById('finished_image_base64').value = dataUrl;

            // プレビュー表示
            const preview = document.getElementById('finished_preview');
            const placeholder = document.getElementById('finished_placeholder');
            const clearBtn = document.getElementById('clear_finished_image_btn');

            if (preview) { preview.src = dataUrl; preview.style.display = 'block'; }
            if (placeholder) { placeholder.style.display = 'none'; }
            if (clearBtn) { clearBtn.style.display = 'block'; }
        };
        reader.readAsDataURL(file);
    });

    // 手順画像をbase64変換＋プレビュー表示
    function bindStepImageEvent(fileInputId, hiddenInputId, stepNum) {
        const fileInput = document.getElementById(fileInputId);
        if (!fileInput) return;
        fileInput.addEventListener('change', function () {
            const file = this.files[0];
            if (!file) return;
            const reader = new FileReader();
            reader.onload = function (e) {
                const dataUrl = e.target.result;

                // hidden inputに保存
                document.getElementById(hiddenInputId).value = dataUrl;

                // プレビュー表示
                const preview = document.getElementById('step_preview_' + stepNum);
                const icon = document.getElementById('step_icon_' + stepNum);
                const clearBtn = document.getElementById('clear_step_btn_' + stepNum); // 追加

                if (preview) { preview.src = dataUrl; preview.style.display = 'block'; }
                if (icon) { icon.style.display = 'none'; }
                if (clearBtn) { clearBtn.style.display = 'block'; }
            };
            reader.readAsDataURL(file);
        });
    }

    bindStepImageEvent('step_image_file1', 'step_image_base64_1', 1);
    bindStepImageEvent('step_image_file2', 'step_image_base64_2', 2);
    bindStepImageEvent('step_image_file3', 'step_image_base64_3', 3);