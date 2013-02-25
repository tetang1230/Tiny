<?php
/**
 *  CSVFile.php
 *
 *  @author      Yoshinobu Kinugasa <kinugasa@gree.co.jp>
 *  @package     Cascade_Driver
 *  @subpackage  Config
 */

/**
 *  CSVFileによる設定データ管理
 *
 *  @author      Yoshinobu Kinugasa <kinugasa@gree.co.jp>
 *  @package     Cascade_Driver
 *  @subpackage  Config
 */
final class    Cascade_Driver_Config_CSVFile
    extends    Cascade_Driver_Config_Common
    implements Cascade_Driver_Config
{
    // ----[ Class Constants ]----------------------------------------
    // {{{ CONSTANT
    /**
     *  行の終端
     */
    const STREAM_LINE_ENDING  = PHP_EOL;

    /**
     *  バッファ領域
     */
    const STREAM_LINE_LENGTH  = 1024;

    /**
     *  CSVのデータ区切り
     */
    const SEPARATOR_DELIMITER = ',';

    /**
     *  データの囲い込み文字
     */
    const SEPARATOR_ENCLOSURE = '"';
    // }}}
    // ----[ Properties ]---------------------------------------------
    // {{{ ERROR
    /**
     *  エラー・コード
     *  @var  int
     */
    protected $error_code     = 0;

    /**
     *  エラー・メッセージ
     *  @var  string
     */
    protected $error_message  = NULL;
    // }}}
    // ----[ Methods ]------------------------------------------------
    // {{{ is_enable
    /**
     *  利用可能なドライバかを確認する
     *
     *  PHPの拡張モジュールの読み込み状態や、<br />
     *  バージョン情報を考慮しドライバの有効の有無を判断する
     *
     *  @return  boolean  TRUE:利用可能ドライバ
     */
    public static /** boolean */
        function is_enable(/** void */)
    {
        return TRUE;
    }
    // }}}
    // {{{ get_version
    /**
     *  ドライバーのバージョン情報を取得する
     *
     *  @return  int  バージョン情報
     */
    public static /* string */
        function get_version(/* void */)
    {
        return $version = '0.1';
    }
    // }}}
    // ----[ Methods ]------------------------------------------------
    // {{{ config_file_load
    /**
     *  CSVファイルデータを読み込む
     *
     *  fgetcsv関数はマルチバイトを含む場合に
     *  ポインター操作に問題がありデータが壊れるので自前で実装する
     *
     *  @param   string   設定ファイルPATH
     *  @param   string   (optional) 配列KEYを指定する場合
     *  @return  array    読み込み結果データ
     */
    protected /* array */
        function config_file_load(/* string */ $file_path,
                                  /* string */ $index = NULL)
    {
        // ファイル・リソースの取得
        if (($fp = @fopen($file_path, 'rb')) === FALSE) {
            $error = 'Could not open a file {file_path} %s';
            $this->error_code    = -1;
            $this->error_message = sprintf($error, $file_path);
            return FALSE;
        }

        // 読み込み
        $line        = 1;
        $file_data   = array();
        $field_names = NULL;
        do {
            // 1行読み込む
            $buffer = stream_get_line($fp, self::STREAM_LINE_LENGTH, self::STREAM_LINE_ENDING);
            if ($buffer === FALSE) {
                $error = 'Failed to read buffer from file {file_path} %s';
                $this->error_code    = -1;
                $this->error_message = sprintf($error, $file_path);
                return FALSE;
            }
            // バッファを解析
            $data = $this->stream_line_process($buffer);
            if ($field_names === NULL) {
                $field_names = $data;
            } else {
                if (count($data) !== count($field_names)) {
                    $error = 'Mismatch field count {file_path, line} %s %d';
                    $this->error_code   = -1;
                    $this->error_messge = sprintf($error, $file_path, $line);
                    return FALSE;
                }
                foreach ($field_names as $pos => $name) {
                    $file_data[$data[0]][$name] = $data[$pos];
                }
            }
            $line ++;
        } while (feof($fp) === FALSE);

        // ファイル・リソースを解放
        fclose($fp);

        // 正常終了
        return $file_data;
    }
    // }}}
    // {{{ stream_line_process
    /**
     *  ライン文字列をパースする処理
     *
     *  @param  string  文字列バッファ
     */
    protected /* void */
        function stream_line_process(/* string */ $buffer)
    {
        // 変数の初期化
        $seek_pos   = 0;
        $seek_len   = 0;
        $cur_pos    = 0;
        $data       = array();
        $buffer_len = strlen($buffer);
        $pattern    = sprintf('/^[\s]*[%s]?(\S*?)[%s]?[\s]*$/mi',
                              self::SEPARATOR_ENCLOSURE,
                              self::SEPARATOR_ENCLOSURE);

        // ラインバッファを解析
        do {
            // データ区切り文字で分割する準備
            $cur_pos = strpos($buffer, self::SEPARATOR_DELIMITER, $cur_pos);
            if ($cur_pos === FALSE) {
                $cur_pos = $buffer_len;
            }
            $seek_len = $cur_pos - $seek_pos;
            // 文字列の囲い込みを確認
            if ($cur_pos !== $buffer_len) {
                $count_enc = substr_count($buffer, self::SEPARATOR_ENCLOSURE, $seek_pos, $seek_len);
                if ($count_enc === 1) {
                    $cur_pos++;
                    continue;
                }
            }
            // データ取り出し
            $token = substr($buffer, $seek_pos, $seek_len);
            $token = preg_replace($pattern, '$1', $token);
            $data[] = $token;
            // ポインターを進める
            $seek_pos += $seek_len + strlen(self::SEPARATOR_DELIMITER);
            $cur_pos   = $seek_pos;
        } while ($seek_pos < $buffer_len);

        // 解析したデータを返す
        return $data;
    }
    // }}}
};