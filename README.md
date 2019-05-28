# file-upload

EX:

$config = [
   'returnMessage' => false, => 是否回傳錯誤訊息
   'allowedType' => '*', => 可支援檔案類型，預設為*
   'typeOf' => 'img', => 檔案類型
   'rename' => false, => 重新命名，預設false
   'compress' => false, => 壓縮圖,預設false
   'width' => '150', => 壓縮圖(指定寬)
   'height' => '150' => 壓縮圖(指定高)
];
*備註 : 
1.如果要進行檔案壓縮，typeOf務必為img
2.如果壓縮沒同時指定寬與高，則會自東壓縮原本比例的0.5

run(檔案名稱, $config);
*備註 : 檔案名稱為form input file的name
