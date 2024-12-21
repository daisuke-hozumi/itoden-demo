var blnIsFirstErr = true;
var blnIsScroll = false;

function funcInputCheck(strFormType, strAllowList = "") {
    blnIsFirstErr = true;
    blnIsScroll = false;
    let blnIsSuccess = true;

    // strAllowListをカンマ区切りで配列に変換
    const allowListArray = strAllowList.split(",").map(item => item.trim());

    // 汎用的なフォーム要素をリスト化（フラグを使って個別のチェックを設定）
    const fieldsToCheck = [
        { name: "Name", type: "txt", required: true, stringOnly: true },  // 氏名
        { name: "Mail", type: "txt", required: true, email: true },       // メールアドレス
        { name: "Tel", type: "txt", required: true, numericOnly: true },  // 電話番号
        { name: "Area", type: "cmb", required: true },                    // 地区選択
        { name: "Tanka", type: "txt", required: true, numericOnly: true, positiveOnly: true }, // 電力料金単価
        { name: "Place", type: "cmb", required: true },                   // 設置場所の形状
        { name: "Menseki", type: "txt", required: true, numericOnly: true, positiveOnly: true }, // 設置面積
        { name: "Angle", type: "cmb", required: true },                   // 傾斜角度
        { name: "Direction", type: "cmb", required: true },               // 設置方位
        { name: "Usage", type: "cmb", required: true }                    // 電力使用区分
    ];

    const frmInput = document.forms[strFormType];

    fieldsToCheck.forEach(field => {
        const fullName = field.type + field.name;
        
        // 無視リストにある場合はスキップ
        if (allowListArray.includes(fullName)) return;

        // 要素が存在しない場合はスキップ
        const elTarget = frmInput.elements[`${field.type.toLowerCase()}${field.name}`];
        if (!elTarget) return;

        const elErr = document.querySelector(`form[name="${strFormType}"] .err_${field.name}`);
        if (!elErr) return;

        // チェックを実施し、エラー時はフラグを更新
        let isError = false;

        // 必須チェック
        if (field.required && funcIsEmpty(elTarget.value.trim())) {
            isError = true;
            elErr.textContent = "この項目は必須です。";
        }
        // 数値のみチェック
        else if (field.numericOnly && !/^\d+$/.test(elTarget.value.trim())) {
            isError = true;
            elErr.textContent = "半角数字のみを入力してください。";
        }
        // 正の値チェック
        else if (field.positiveOnly && (Number(elTarget.value.trim()) <= 0)) {
            isError = true;
            elErr.textContent = "0より大きい数値を入力してください。";
        }
        // 文字列のみチェック
        else if (field.stringOnly && !/^[A-Za-z\u3040-\u30FF\u4E00-\u9FAF]+$/.test(elTarget.value.trim())) {
            isError = true;
            elErr.textContent = "有効な文字列を入力してください。";
        }
        // メール形式チェック
        else if (field.email && !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(elTarget.value.trim())) {
            isError = true;
            elErr.textContent = "有効なメールアドレスを入力してください。";
        }

        // エラー表示・非表示を管理
        funcIsErr(elTarget, elErr, isError);
        if (isError) blnIsSuccess = false;

        // リアルタイムエラー解除のためのイベントリスナーを追加
        if (field.type === "txt") {
            elTarget.addEventListener("input", () => validateField(elTarget, elErr, field));
        } else if (field.type === "cmb") {
            elTarget.addEventListener("change", () => validateField(elTarget, elErr, field));
        }
    });

    return blnIsSuccess;
}

// 個別フィールドのバリデーション
function validateField(elTarget, elErr, field) {
    let isError = false;

    // 必須チェック
    if (field.required && funcIsEmpty(elTarget.value.trim())) {
        isError = true;
        elErr.textContent = "この項目は必須です。";
    }
    // 数値のみチェック
    else if (field.numericOnly && !/^\d+$/.test(elTarget.value.trim())) {
        isError = true;
        elErr.textContent = "半角数字のみを入力してください。";
    }
    // 正の値チェック
    else if (field.positiveOnly && (Number(elTarget.value.trim()) <= 0)) {
        isError = true;
        elErr.textContent = "0より大きい数値を入力してください。";
    }
    // 文字列のみチェック
    else if (field.stringOnly && !/^[A-Za-z\u3040-\u30FF\u4E00-\u9FAF]+$/.test(elTarget.value.trim())) {
        isError = true;
        elErr.textContent = "有効な文字列を入力してください。";
    }
    // メール形式チェック
    else if (field.email && !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(elTarget.value.trim())) {
        isError = true;
        elErr.textContent = "有効なメールアドレスを入力してください。";
    }

    funcIsErr(elTarget, elErr, isError);
}

// 未入力チェック
function funcIsEmpty(strTarget) {
    return strTarget.trim() === "";
}

// エラー表示・解除処理
function funcIsErr(elInput, elErr, blnIsError) {
    if (blnIsError) {
        elInput.classList.add("error-border");
        elErr.classList.add("error-visible");

        // 最初のエラーにスクロールし、以降はスクロールしない
        if (!blnIsScroll) {
            const offsetTop = elInput.getBoundingClientRect().top + window.pageYOffset - 10; // スクロールオフセットを少し調整
            window.scrollTo({ top: offsetTop, behavior: 'smooth' });
            elInput.focus(); // 最初のエラー要素にフォーカス
            blnIsScroll = true;
        }
    } else {
        elInput.classList.remove("error-border");
        elErr.classList.remove("error-visible");
    }
}
