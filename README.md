# 簡單檔案上傳 file upload

下面簡單敘述檔案上傳的流程

1. 前端
```html
<form action="testfunction/run" method="post" enctype="multipart/form-data">
  <input type="file" name="file">
  <input type="submit" value="send">
</from>
```

2. 後端
```php
<?php
$config = [
  'returnMessage' => false, => 回傳錯誤訊息
  'uploadPath' => '', => 檔案上傳位置
  'allowedType' => '*', => 支援檔案類型
  'typeOf' => 'img', => 檔案類型
  'rename' => false, => 重新命名
  'compress' => false, => 壓縮圖檔
  'width' => '150', => 壓縮寬
  'height' => '150' => 壓縮高
];

run('file', $config);

```
*備註 :
1. $config['uploadPath'] 如不在$config，預設位置為 ./public/uploads/
2. $config['allowedType'] 可填寫其他格式，EX: ['jpg','gif'] 用array方式呈現
3. 圖片壓縮只支援 'gif', 'jpg', 'png', 'bmp', 'jpeg' 格式
4. 如果沒有同時指定壓縮寬與高，則會自動壓縮原比例的0.5