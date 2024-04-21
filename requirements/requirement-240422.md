# 需求-20240422

- 將原本利用 session 的 **todo list** 調整成利用檔案來儲存的版本, 檔案的放置路徑可以自行決定

> [!TIPS]
> 以下 function 可以加入到 [需求-20240411](./requirement-240411.md) 建立的 helpers.php 或 function.php 中 (取決於你的檔案名稱)

- 建立幾項工具函數

  1. 建立 base_path 函數 `base_path($path)`

     - 功能: 取得專案根目錄的路徑或特定檔案(目錄)的路徑

     - 參數說明:

       - $path 指定檔案或目錄的相對路徑, 字串, 預設值為空字串

     - 功能細節:

       - 當 $path 是空字串時, 返回專案根目錄的絕對路徑
       - 當 $path 不是空字串時, 返回專案下該指定目標的路徑

       ```php
       $path = base_path();

       // 'C:\xampp\htdocs\simple-demo' or '/var/www/html/simple-demo'

       $path = base_path('app/helpers');

       // 'C:\xampp\htdocs\simple-demo\app/helpers.php' or '/var/www/html/simple-demo/app/helpers.php'
       ```

  1. 建立 get_request_query 函數 `get_request_query($key, $default)`

     - 功能: 取得請求的 query 中的所有 key-value pair

     - 參數說明:

       - $key 是想取得值的指定 key , 字串/null, 預設值是 null
       - $default 是找不到 $key 時返回的預設值, 字串/null, 預設值是 null

     - 功能細節:

       - 當 $key 是 _null_ 時, 返回請求中所有 query 的 key-value pair
       - 當 $key 是 _字串_ 時, 返回 $key 所指定的值; 若找不到, 則返回 $default

       ```php
       $querys = get_request_query();

       /*
           [
               'key1' => 'value1',
               'key2' => 'value2',
               'key3' => 'value3',
           ]
       */

       $key1 = get_request_query('key1');

       // 'value1'

       $key4 = get_request_query('key4', 'not exists');

       // 'not exists'
       ```

  1. 建立 get_request_post 函數 `get_request_post($key, $default)`

     - 功能: 取得請求的 post data 中的所有 key-value pair, 功能本質上與前者 `get_request_query` 相似, 但資料來源是 post data

     - **請留意 post data 是 JSON 時也要能夠解析**

  1. 建立 get_request_input 函數 `get_request_input($key, $default)`

     - 功能: 取得請求的 query 和 post data 的所有 key-value pair, 以 post data 的資料為優先

  1. 建立 get_request_file 函數 `get_request_file($key, $default)`

     - 功能: 取得請求提交的檔案的資訊

     - 參數說明:

       - $key 是想取得值的指定 key , 字串/null, 預設值是 null
       - $default 是找不到 $key 時返回的預設值, 字串/null, 預設值是 null

     - 功能細節:

       - 當 $key 是 null 時, 返回請求提交的所有檔案資訊
       - 當 $key 是 字串 時, 返回 $key 所指定的檔案資訊; 若找不到, 則返回 $default

       ```php
       $files = get_request_query();

       /*
           [
               'file' => [
                   'name' => 'test.png'
                   'full_path' => 'test.png'
                   'type' => 'image/png'
                   'tmp_name' => 'C:\xampp\tmp\php96C9.tmp'
                   'error' => 0
                   'size' => 39269
               ]
           ]
       */
       ```

  1. 建立 get_request_all 函數 `get_request_all($key, $default)`

     - 功能: 取得請求的 input 和 file 的所有 key-value pair, 以 input 的資料為優先
