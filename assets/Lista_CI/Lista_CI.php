<?php

/**
 * create a folder named assets on base application path (same level as application folder).
 * copy the controller Lista_CI into controllers folder.
 */
class Lista_CII
{
  private $_database;
  private $_limit;
  private $_offset;
  private $_colorClass;
  private $_ID;
  private $_currentPage;
  private $_currentOrder;
  private $_currentOrderAD;
  private $_orderArrow;
  private $_pageSlideItens;
  private $_baseUrl;
  private $_arrSql;
  private $_arrAlign;

  function __construct($database)
  {
    $this->_database       = $database;
    $this->_limit          = 50;
    $this->_offset         = 0;
    $this->_ID             = "table_".date("YmdHis");
    $this->_colorClass     = "success";
    $this->_currentPage    = 1;
    $this->_currentOrder   = "";
    $this->_currentOrderAD = "";
    $this->_orderArrow     = "";
    $this->_pageSlideItens = 3;
    $this->_baseUrl        = (function_exists("base_url")) ? base_url(): "";

    $this->_arrSql           = [];
    $this->_arrSql["fields"] = [];
    $this->_arrSql["tables"] = [];
    $this->_arrSql["joins"]  = [];
    $this->_arrSql["where"]  = [];
    $this->_arrSql["order"]  = "";
    $this->_arrAlign         = [];
  }

  public function configFromJsonStr($jsonStr)
  {
    $vDecodedJson = $this->base64url_decode($jsonStr);
    $arrJson      = json_decode($vDecodedJson, 1);
    if (count($arrJson) <= 0) {
      return false;
    }

    foreach ($arrJson as $variable => $value) {
      $this->$variable = $value;
    }
  }

  private function generateJsonConfig()
  {
    $arrClassVars = get_object_vars($this);
    $arrJson      = $arrClassVars;
    unset($arrJson["_database"]);

    $encodedJsonStr = json_encode($arrJson);
    return $this->base64url_encode($encodedJsonStr);
  }

  public function base64url_encode($data)
  {
    return base64_encode(urlencode($data));
  }

  public function base64url_decode($data)
  {
    return urldecode(base64_decode($data));
  }

  public function setLimit($limit)
  {
    $this->_limit = $limit;
  }

  public function setOffset($offset)
  {
    $this->_offset = $offset;
  }

  /**
   * $align: L->Left, C->Center, R->Right, I->Invisible
   */
  public function addField($fieldName, $align="C")
  {
    $this->_arrSql["fields"][] = $fieldName;
    $this->_arrAlign[]         = $align;
  }

  public function addFrom($table)
  {
    $this->_arrSql["tables"][] = $table;
  }

  public function addJoin($join)
  {
    $this->_arrSql["joins"][] = $join;
  }

  public function addWhere($where)
  {
    $this->_arrSql["where"][] = $where;
  }

  public function setOrder($order)
  {
    $this->_arrSql["order"] = $order;
  }

  private function generateSql($countOnly=false)
  {
    $V_SQL  = "SELECT ";
    if($countOnly){
      $V_SQL .= " COUNT(*) AS cnt ";
    } else {
      $V_SQL .= implode(",", $this->_arrSql["fields"]);
    }
    $V_SQL .= " FROM " . implode(",", $this->_arrSql["tables"]);
    $V_SQL .= implode(" ", $this->_arrSql["joins"]);
    $V_SQL .= " WHERE TRUE ";
    if(count($this->_arrSql["where"]) > 0){
      $V_SQL .= implode(" AND ", $this->_arrSql["where"]);
    }
    if($this->_arrSql["order"] != "" && !$countOnly){
      $V_SQL .= " ORDER BY " . $this->_arrSql["order"];
    }
    if(!$countOnly){
      $V_SQL .= " LIMIT " . $this->_limit . " OFFSET " . $this->_offset;
    }

    return $V_SQL;
  }

  public function changePage($pageNr)
  {
    $this->_currentPage = $pageNr;
  }

  public function changeOrderCol($colNbr)
  {
    $lastOrder           = $this->_currentOrder;
    $this->_currentOrder = $colNbr;

    $size    = "5px";
    $baseCss = "width:0px; height:0px; border-left:$size solid transparent; border-right:$size solid transparent; display:inline-block;";
    $color   = "#4caf50";

    if($this->_currentOrderAD == "" || $this->_currentOrderAD == "DESC" || $lastOrder != $this->_currentOrder){
      $this->_currentOrderAD = "ASC";
      $this->_orderArrow     = "<div class='arrow-up' style='$baseCss border-bottom: $size solid $color;'></div>";
    } else {
      $this->_currentOrderAD = "DESC";
      $this->_orderArrow     = "<div class='arrow-up' style='$baseCss border-top: $size solid $color;'></div>";
    }

    $this->_arrSql["order"]  = $this->_currentOrder . " " . $this->_currentOrderAD;
  }

  public function setColorClass($colorClass)
  {
    $this->_colorClass = $colorClass;
  }

  public function setBaseUrl($baseUrl)
  {
    $this->_baseUrl = $baseUrl;
  }

  private function getAlignStrByIdx($idx)
  {
    $align = $this->_arrAlign[$idx] ?? "C";
    switch ($align) {
      case "L":
        $alignStr = "left";
        break;
      case "C":
        $alignStr = "center";
        break;
      case "R":
        $alignStr = "right";
        break;
      default:
        $alignStr = "center";
        break;
    }

    return $alignStr;
  }

  private function getJsFunction($filter="", $filter_val="", $changePage=0, $orderBy="")
  {
    #filter_lista_ci(url_request_base, lista_ci_id, filter, filter_val, changePage, orderBy)
    return "filter_lista_ci('".$this->_baseUrl."', '".$this->_ID."', '$filter', '$filter_val', $changePage, '$orderBy')";
  }

  public function getHtmlTable()
  {
    $this->_offset = ($this->_currentPage - 1) * $this->_limit;
    
    $V_SQL         = $this->generateSql(false);
    $V_SQL_COUNT   = $this->generateSql(true);

    $query           = $this->_database->query($V_SQL_COUNT);
    $row             = $query->row();
    $V_TOTAL_RECORDS = $row->cnt ?? 0;
    $V_TOTAL_PAGES   = ceil($V_TOTAL_RECORDS / $this->_limit);
    $res             = $this->_database->query($V_SQL);

    $V_ARR_HEADER = [];
    $V_ARR_BODY   = [];
    foreach ($res->result() as $row) {
      if (empty($V_ARR_HEADER)) {
        $V_ARR_HEADER = array_keys((array) $row);
      }

      $V_ARR_BODY[] = (array) $row;
    }

    $V_HTML = "<table class='table' id='".$this->_ID."'>";
    $V_HTML .= "  <thead class='text-".$this->_colorClass."'>";
    $V_HTML .= "    <tr>";
    $i       = 0;
    foreach ($V_ARR_HEADER as $title) {
      $strAlign = $this->getAlignStrByIdx($i);
      $colNbr   = $i + 1;
      $arrow    = ($colNbr == $this->_currentOrder) ? $this->_orderArrow: "";

      $V_HTML .= "    <td align='$strAlign'>";
      $V_HTML .= "      <a class='text-".$this->_colorClass."' style='font-weight: bold;' href='javascript:;' onClick=\"".$this->getJsFunction("", "", 0, $colNbr)."\">";
      $V_HTML .= "        $title";
      $V_HTML .= "      </a>";
      $V_HTML .= "      $arrow";
      $V_HTML .= "    </td>";
      $i++;
    }
    $V_HTML .= "    </tr>";
    $V_HTML .= "  </thead>";
    $V_HTML .= "  <tbody>";
    foreach ($V_ARR_BODY as $row) {
      $V_HTML .= "  <tr>";
      $i       = 0;
      foreach ($row as $column) {
        $strAlign = $this->getAlignStrByIdx($i);

        $V_HTML .= "  <td align='$strAlign'>$column</td>";
        $i++;
      }
      $V_HTML .= "  </tr>";
    }
    $V_HTML .= "  </tbody>";
    $V_HTML .= "  <tfoot>";
    $V_HTML .= "    <tr>";
    $V_HTML .= "      <td colspan='".count($V_ARR_HEADER)."'>";
    if ($V_TOTAL_PAGES > 1) {
      $OBJ_BASE_64 = $this->generateJsonConfig();

      $V_HTML .= "      <ul class='pagination pagination-".$this->_colorClass."'>";
      $V_HTML .= "        <li class='page-item'>";
      $V_HTML .= "          <a href='javascript:;' onClick=\"".$this->getJsFunction("", "", 1)."\" class='item item-".$this->_colorClass." page-link'>&#60; Primeira</a>";
      $V_HTML .= "        </li>";

      $V_INI_LIMIT = ($this->_currentPage - $this->_pageSlideItens) < 1 ? 1 : ($this->_currentPage
        - $this->_pageSlideItens);
      $V_END_LIMIT = ($this->_currentPage + $this->_pageSlideItens) > $V_TOTAL_PAGES
          ? $V_TOTAL_PAGES : ($this->_currentPage + $this->_pageSlideItens);

      for ($i = $V_INI_LIMIT; $i <= $V_END_LIMIT; $i++) {
        $cssClass = ($i == $this->_currentPage) ? " active " : "";

        $V_HTML .= "      <li class='page-item'>";
        $V_HTML .= "        <a href='javascript:;' onClick=\"".$this->getJsFunction("", "", $i)."\" class='item item-".$this->_colorClass." page-link $cssClass'>$i</a>";
        $V_HTML .= "      </li>";
      }
      $V_HTML .= "        <li class='page-item'>";
      $V_HTML .= "          <a href='javascript:;' onClick=\"".$this->getJsFunction("", "", $V_TOTAL_PAGES)."\" class='item item-".$this->_colorClass." page-link'>Última &#62;</a>";
      $V_HTML .= "        </li>";
      $V_HTML .= "      </ul>";
    }
    $V_HTML .= "      </td>";
    $V_HTML .= "    </tr>";
    $V_HTML .= "  </tfoot>";
    $V_HTML .= "</table>";

    $V_HTML_RET  = "<span class='spn_Lista_CI' id='spn_".$this->_ID."'>";
    $V_HTML_RET .= "  $V_HTML";
    $V_HTML_RET .= "  <input type='hidden' id='hddn_".$this->_ID."' value='$OBJ_BASE_64' />";
    $V_HTML_RET .= "</span>";
    return $V_HTML_RET;
  }
}