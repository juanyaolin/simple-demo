# 需求-20240411

1.  申請 gitHub 帳號

1.  建立一個 public 的 git 專案, 專案名稱隨意(之後還能再改)

1.  建立一個 **index.php** 檔案, 並啟用型別限制(強型別, 嚴格型別)

    > [!TIP]
    > 專案的檔案(資料夾)結構可以自行決定, 之後也可以再調整

1.  安裝 [VarDumper](https://symfony.com/doc/current/components/var_dumper.html) 並引入至 index.php

    > [!NOTICE]
    > 請注意 .gitignore 檔案，不要將 **composer.lock 檔案** 和 **vendor 資料夾** 上傳

1.  建立一個 php 檔案並引入到 index.php

    > [!TIP]
    > 檔案名稱可以自由設定, 例如: _functions.php_ 或 _helpers.php_ 都可以

    1.  建立 data_get 函數 `data_get($target, $key, $default)`

        - 功能: 從 $target 中取得編號或鍵是 $key 的值, 如果沒有則取得 $default

        - 參數說明:

          - $target 是要查找的變數, 任意類型, 沒有預設值
          - $key 是要查找的目標, 陣列/字串/整數/null, 沒有預設值
          - $default 是當 $target 不存在 $key 時預計返回的值, 任意類型, 預設值為 null

        - 功能細節:

          - 當 $target 是 _null_ 時, 返回 $target
          - 當 $key 是 _null_ 時, 返回 $target
          - 當 $target 是陣列時, 從 $target 中找到編號或鍵是 $key 的值, 並且要能夠以「.」或陣列的方式**巢狀搜尋**, 範例如下

            ```
            一般搜尋:
            'key1' 或 [key1] 要能得到 value1
            'key3' 或 [key3] 要能得到 ['key3-1' => value3-1]

            $target = [
                'key1' => value1,
                'key2' => value2,
                'key3' => [
                    'key3-1' => value3-1,
                ]
            ];

            --------------------------------------------------

            巢狀搜尋:
            'key1.key2.key3' 或 [key1, key2, key3] 要找到 value

            $target = [
                key1 => [
                    key2 => [
                        key3 => value
                    ]
                ]
            ]
            ```

          - 當在 $target 中找不到 $key 時, 返回 $default

    1.  以上函數只能建立一次, 可以利用 `function_exists()` 做判斷
