<?php
session_start();

require_once 'lib.php';

if (isset($_GET["order"]))
    $order = @$_GET["order"];
if (isset($_GET["type"]))
    $ordtype = @$_GET["type"];

if (isset($_POST["filter"]))
    $filter = @$_POST["filter"];
if (isset($_POST["filter_field"]))
    $filterfield = @$_POST["filter_field"];
$wholeonly = false;
if (isset($_POST["wholeonly"]))
    $wholeonly = @$_POST["wholeonly"];

if (!isset($order) && isset($_SESSION["order"]))
    $order = $_SESSION["order"];
if (!isset($ordtype) && isset($_SESSION["type"]))
    $ordtype = $_SESSION["type"];
if (!isset($filter) && isset($_SESSION["filter"]))
    $filter = $_SESSION["filter"];
if (!isset($filterfield) && isset($_SESSION["filter_field"]))
    $filterfield = $_SESSION["filter_field"];
?>

<html>
    <head>
        <title>AgendaRS - [Contato (Tipos)]</title>
        <meta name="generator" http-equiv="content-type" content="text/html">
        <link rel="stylesheet" type="text/css" href="style.css">
    </head>
    <body>
        <table class="bd" width="100%"><tr><td class="hr"><h2>AgendaRS - [Contato (Tipos)]</h2></td></tr></table>
        <table width="100%">
            <tr>

                <td width="10%" valign="top">
                    <h3>Cadastro</h3>
            <li><a href="contato.php?a=reset">Contato</a>
            <li><a href="telefone.php?a=reset">Telefone</a>
            <li><a href="email.php?a=reset">e-mail</a>
            <li><a href="endereco.php?a=reset">Endere&ccedil;o</a>
            <li><a href="tarefa.php?a=reset">Tarefa</a>
                <h3>Tipos</h3>
            <li><a href="tipocontato.php?a=reset">Contato</a>
            <li><a href="tipoemail.php?a=reset">e-mail</a>
            <li><a href="tipoendereco.php?a=reset">Endere&ccedil;o</a>
            <li><a href="tipotelefone.php?a=reset">Telefone</a>
                <h3>Associar ao Contato</h3>
            <li><a href="contatoemails.php?a=reset">e-mail</a>
            <li><a href="contatoenderecos.php?a=reset">Endere&ccedil;o</a>
            <li><a href="contatotelefones.php?a=reset">Telefone</a>
            <li>Tipo de Contato
                <h3>Configura&ccedil;&atilde;o Tarefa</h3>
            <li><a href="tarefaprioridade.php?a=reset">Prioridade</a>
            <li><a href="tarefastatus.php?a=reset">Status</a>
                </td>
            <td width="5%">
            </td>
            <td bgcolor="#e0e0e0">
            </td>
            <td width="5%">
            </td>
            <td width="80%" valign="top">
                <?php
                $conn = connect();
                $showrecs = 20;
                $pagerange = 10;

                $a = @$_GET["a"];
                $recid = @$_GET["recid"];
                $page = @$_GET["page"];
                if (!isset($page))
                    $page = 1;

                $sql = @$_POST["sql"];

                switch ($sql) {
                    case "insert":
                        sql_insert();
                        break;
                    case "update":
                        sql_update();
                        break;
                    case "delete":
                        sql_delete();
                        break;
                }

                switch ($a) {
                    case "add":
                        addrec();
                        break;
                    case "view":
                        viewrec($recid);
                        break;
                    case "edit":
                        editrec($recid);
                        break;
                    case "del":
                        deleterec($recid);
                        break;
                    default:
                        select();
                        break;
                }

                if (isset($order))
                    $_SESSION["order"] = $order;
                if (isset($ordtype))
                    $_SESSION["type"] = $ordtype;
                if (isset($filter))
                    $_SESSION["filter"] = $filter;
                if (isset($filterfield))
                    $_SESSION["filter_field"] = $filterfield;
                if (isset($wholeonly))
                    $_SESSION["wholeonly"] = $wholeonly;

                mysqli_close($conn);
                ?>
            </td></tr></table>
    <table class="bd" width="100%"><tr><td class="hr">Todos os direitos reservados</td></tr></table>
</body>
</html>

<?php

function select() {
    global $a;
    global $showrecs;
    global $page;
    global $filter;
    global $filterfield;
    global $wholeonly;
    global $order;
    global $ordtype;


    if ($a == "reset") {
        $filter = "";
        $filterfield = "";
        $wholeonly = "";
        $order = "";
        $ordtype = "";
    }

    $checkstr = "";
    if ($wholeonly)
        $checkstr = " checked";
    if ($ordtype == "asc") {
        $ordtypestr = "desc";
    } else {
        $ordtypestr = "asc";
    }
    $res = sql_select();
    $count = sql_getrecordcount();
    if ($count % $showrecs != 0) {
        $pagecount = intval($count / $showrecs) + 1;
    } else {
        $pagecount = intval($count / $showrecs);
    }
    $startrec = $showrecs * ($page - 1);
    if ($startrec < $count) {
        mysqli_data_seek($res, $startrec);
    }
    $reccount = min($showrecs * $page, $count);
    ?>
    <table class="bd" border="0" cellspacing="1" cellpadding="4">
        <tr><td>Cadastro: contatotipos</td></tr>
        <tr><td>Exibindo registros <?php echo $startrec + 1 ?> - <?php echo $reccount ?> of <?php echo $count ?></td></tr>
    </table>
    <hr size="1" noshade>
    <form action="contatotipos.php" method="post">
        <table class="bd" border="0" cellspacing="1" cellpadding="4">
            <tr>
                <td><b>Filtros</b>&nbsp;</td>
                <td><input type="text" name="filter" value="<?php echo $filter ?>"></td>
                <td><select name="filter_field">
                        <option value="">Todos os Campos</option>
                        <option value="<?php echo "lp_codigoContato" ?>"<?php
    if ($filterfield == "lp_codigoContato") {
        echo "selected";
    }
    ?>><?php echo htmlspecialchars("Contato") ?></option>
                        <option value="<?php echo "lp_codigoTipoContato" ?>"<?php
                            if ($filterfield == "lp_codigoTipoContato") {
                                echo "selected";
                            }
    ?>><?php echo htmlspecialchars("Tipo Contato") ?></option>
                    </select></td>
                <td><input type="checkbox" name="wholeonly"<?php echo $checkstr ?>>Coincidir palavras completas</td>
                </td></tr>
            <tr>
                <td>&nbsp;</td>
                <td><input type="submit" name="action" value="Filtrar"></td>
                <td><a href="contatotipos.php?a=reset">Limpar Filtro</a></td>
            </tr>
        </table>
    </form>
    <hr size="1" noshade>
    <?php showpagenav($page, $pagecount); ?>
    <br>
    <table class="tbl" border="0" cellspacing="1" cellpadding="5"width="100%">
        <tr>
            <td class="hr">&nbsp;</td>
            <td class="hr">&nbsp;</td>
            <td class="hr">&nbsp;</td>
            <td class="hr"><a class="hr" href="contatotipos.php?order=<?php echo "lp_codigoContato" ?>&type=<?php echo $ordtypestr ?>"><?php echo htmlspecialchars("Contato") ?></a></td>
            <td class="hr"><a class="hr" href="contatotipos.php?order=<?php echo "lp_codigoTipoContato" ?>&type=<?php echo $ordtypestr ?>"><?php echo htmlspecialchars("Tipo Contato") ?></a></td>
        </tr>
        <?php
        for ($i = $startrec; $i < $reccount; $i++) {
            $row = mysqli_fetch_assoc($res);
            $style = "dr";
            if ($i % 2 != 0) {
                $style = "sr";
            }
            ?>
            <tr>
                <td class="<?php echo $style ?>"><a href="contatotipos.php?a=view&recid=<?php echo $i ?>">Ver</a></td>
                <td class="<?php echo $style ?>"><a href="contatotipos.php?a=edit&recid=<?php echo $i ?>">Editar</a></td>
                <td class="<?php echo $style ?>"><a href="contatotipos.php?a=del&recid=<?php echo $i ?>">Excluir</a></td>
                <td class="<?php echo $style ?>"><?php echo htmlspecialchars($row["lp_codigoContato"]) ?></td>
                <td class="<?php echo $style ?>"><?php echo htmlspecialchars($row["lp_codigoTipoContato"]) ?></td>
            </tr>
            <?php
        }
        mysqli_free_result($res);
        ?>
    </table>
    <br>
    <?php showpagenav($page, $pagecount); ?>
<?php } ?>

<?php

function showrow($row, $recid) {
    ?>
    <table class="tbl" border="0" cellspacing="1" cellpadding="5"width="50%">
        <tr>
            <td class="hr"><?php echo htmlspecialchars("Contato") . "&nbsp;" ?></td>
            <td class="dr"><?php echo htmlspecialchars($row["lp_codigoContato"]) ?></td>
        </tr>
        <tr>
            <td class="hr"><?php echo htmlspecialchars("Tipo Contato") . "&nbsp;" ?></td>
            <td class="dr"><?php echo htmlspecialchars($row["lp_codigoTipoContato"]) ?></td>
        </tr>
    </table>
<?php } ?>

<?php

function showroweditor($row, $iseditmode) {
    global $conn;
    ?>
    <table class="tbl" border="0" cellspacing="1" cellpadding="5"width="50%">
        <tr>
            <td class="hr"><?php echo htmlspecialchars("Contato") . "&nbsp;" ?></td>
            <td class="dr"><select name="codigoContato">
                    <?php
                    $sql = "select `codigoContato`, `nome` from `contato`";
                    $res = mysqli_query($conn, $sql) or die(mysqli_error($conn));

                    while ($lp_row = mysqli_fetch_assoc($res)) {
                        $val = $lp_row["codigoContato"];
                        $caption = $lp_row["nome"];
                        if ($row["codigoContato"] == $val) {
                            $selstr = " selected";
                        } else {
                            $selstr = "";
                        }
                        ?><option value="<?php echo $val ?>"<?php echo $selstr ?>><?php echo $caption ?></option>
                    <?php } ?></select>
            </td>
        </tr>
        <tr>
            <td class="hr"><?php echo htmlspecialchars("Tipo Contato") . "&nbsp;" ?></td>
            <td class="dr"><select name="codigoTipoContato">
                    <?php
                    $sql = "select `codigoTipoContato`, `descricao` from `tipocontato`";
                    $res = mysqli_query($conn, $sql) or die(mysqli_error());

                    while ($lp_row = mysqli_fetch_assoc($res)) {
                        $val = $lp_row["codigoTipoContato"];
                        $caption = $lp_row["descricao"];
                        if ($row["codigoTipoContato"] == $val) {
                            $selstr = " selected";
                        } else {
                            $selstr = "";
                        }
                        ?><option value="<?php echo $val ?>"<?php echo $selstr ?>><?php echo $caption ?></option>
                    <?php } ?></select>
            </td>
        </tr>
    </table>
<?php } ?>

<?php

function showpagenav($page, $pagecount) {
    ?>
    <table class="bd" border="0" cellspacing="1" cellpadding="4">
        <tr>
            <td><a href="contatotipos.php?a=add">Inserir Registro</a>&nbsp;</td>
            <?php if ($page > 1) { ?>
                <td><a href="contatotipos.php?page=<?php echo $page - 1 ?>">&lt;&lt;&nbsp;Anterior</a>&nbsp;</td>
            <?php } ?>
            <?php
            global $pagerange;

            if ($pagecount > 1) {

                if ($pagecount % $pagerange != 0) {
                    $rangecount = intval($pagecount / $pagerange) + 1;
                } else {
                    $rangecount = intval($pagecount / $pagerange);
                }
                for ($i = 1; $i < $rangecount + 1; $i++) {
                    $startpage = (($i - 1) * $pagerange) + 1;
                    $count = min($i * $pagerange, $pagecount);

                    if ((($page >= $startpage) && ($page <= ($i * $pagerange)))) {
                        for ($j = $startpage; $j < $count + 1; $j++) {
                            if ($j == $page) {
                                ?>
                                <td><b><?php echo $j ?></b></td>
                            <?php } else { ?>
                                <td><a href="contatotipos.php?page=<?php echo $j ?>"><?php echo $j ?></a></td>
                                <?php
                            }
                        }
                    } else {
                        ?>
                        <td><a href="contatotipos.php?page=<?php echo $startpage ?>"><?php echo $startpage . "..." . $count ?></a></td>
                        <?php
                    }
                }
            }
            ?>
            <?php if ($page < $pagecount) { ?>
                <td>&nbsp;<a href="contatotipos.php?page=<?php echo $page + 1 ?>">Pr�ximo&nbsp;&gt;&gt;</a>&nbsp;</td>
            <?php } ?>
        </tr>
    </table>
<?php } ?>

<?php

function showrecnav($a, $recid, $count) {
    ?>
    <table class="bd" border="0" cellspacing="1" cellpadding="4">
        <tr>
            <td><a href="contatotipos.php">Pagina Inicial</a></td>
            <?php if ($recid > 0) { ?>
                <td><a href="contatotipos.php?a=<?php echo $a ?>&recid=<?php echo $recid - 1 ?>">Registro Anterior</a></td>
            <?php } if ($recid < $count - 1) { ?>
                <td><a href="contatotipos.php?a=<?php echo $a ?>&recid=<?php echo $recid + 1 ?>">Pr�ximo Registro</a></td>
            <?php } ?>
        </tr>
    </table>
    <hr size="1" noshade>
<?php } ?>

<?php

function addrec() {
    ?>
    <table class="bd" border="0" cellspacing="1" cellpadding="4">
        <tr>
            <td><a href="contatotipos.php">Pagina Inicial</a></td>
        </tr>
    </table>
    <hr size="1" noshade>
    <form enctype="multipart/form-data" action="contatotipos.php" method="post">
        <p><input type="hidden" name="sql" value="insert"></p>
        <?php
        $row = array(
            "codigoContato" => "",
            "codigoTipoContato" => "");
        showroweditor($row, false);
        ?>
        <p><input type="submit" name="action" value="Gravar"></p>
    </form>
<?php } ?>

<?php

function viewrec($recid) {
    $res = sql_select();
    $count = sql_getrecordcount();
    mysql_data_seek($res, $recid);
    $row = mysql_fetch_assoc($res);
    showrecnav("view", $recid, $count);
    ?>
    <br>
    <?php showrow($row, $recid) ?>
    <br>
    <hr size="1" noshade>
    <table class="bd" border="0" cellspacing="1" cellpadding="4">
        <tr>
            <td><a href="contatotipos.php?a=add">Inserir Registro</a></td>
            <td><a href="contatotipos.php?a=edit&recid=<?php echo $recid ?>">Editar Registro</a></td>
            <td><a href="contatotipos.php?a=del&recid=<?php echo $recid ?>">Excluir Registro</a></td>
        </tr>
    </table>
    <?php
    mysql_free_result($res);
}
?>

<?php

function editrec($recid) {
    $res = sql_select();
    $count = sql_getrecordcount();
    mysql_data_seek($res, $recid);
    $row = mysql_fetch_assoc($res);
    showrecnav("edit", $recid, $count);
    ?>
    <br>
    <form enctype="multipart/form-data" action="contatotipos.php" method="post">
        <input type="hidden" name="sql" value="update">
        <input type="hidden" name="xcodigoContato" value="<?php echo $row["codigoContato"] ?>">
        <input type="hidden" name="xcodigoTipoContato" value="<?php echo $row["codigoTipoContato"] ?>">
        <?php showroweditor($row, true); ?>
        <p><input type="submit" name="action" value="Gravar"></p>
    </form>
    <?php
    mysql_free_result($res);
}
?>

<?php

function deleterec($recid) {
    $res = sql_select();
    $count = sql_getrecordcount();
    mysql_data_seek($res, $recid);
    $row = mysql_fetch_assoc($res);
    showrecnav("del", $recid, $count);
    ?>
    <br>
    <form action="contatotipos.php" method="post">
        <input type="hidden" name="sql" value="delete">
        <input type="hidden" name="xcodigoContato" value="<?php echo $row["codigoContato"] ?>">
        <input type="hidden" name="xcodigoTipoContato" value="<?php echo $row["codigoTipoContato"] ?>">
        <?php showrow($row, $recid) ?>
        <p><input type="submit" name="action" value="Confirmar"></p>
    </form>
    <?php
    mysql_free_result($res);
}
?>

<?php

function sql_select() {
    global $conn;
    global $order;
    global $ordtype;
    global $filter;
    global $filterfield;
    global $wholeonly;

    $filterstr = sqlstr($filter);
    if (!$wholeonly && isset($wholeonly) && $filterstr != '')
        $filterstr = "%" . $filterstr . "%";
    $sql = "SELECT * FROM (SELECT t1.`codigoContato`, lp0.`nome` AS `lp_codigoContato`, t1.`codigoTipoContato`, lp1.`descricao` AS `lp_codigoTipoContato` FROM `contatotipos` AS t1 LEFT OUTER JOIN `contato` AS lp0 ON (t1.`codigoContato` = lp0.`codigoContato`) LEFT OUTER JOIN `tipocontato` AS lp1 ON (t1.`codigoTipoContato` = lp1.`codigoTipoContato`)) subq";
    if (isset($filterstr) && $filterstr != '' && isset($filterfield) && $filterfield != '') {
        $sql .= " where " . sqlstr($filterfield) . " like '" . $filterstr . "'";
    } elseif (isset($filterstr) && $filterstr != '') {
        $sql .= " where (`lp_codigoContato` like '" . $filterstr . "') or (`lp_codigoTipoContato` like '" . $filterstr . "')";
    }
    if (isset($order) && $order != '')
        $sql .= " order by `" . sqlstr($order) . "`";
    if (isset($ordtype) && $ordtype != '')
        $sql .= " " . sqlstr($ordtype);
    $res = mysqli_query($conn, $sql) or die(mysql_error());
    return $res;
}

function sql_getrecordcount() {
    global $conn;
    global $order;
    global $ordtype;
    global $filter;
    global $filterfield;
    global $wholeonly;

    $filterstr = sqlstr($filter);
    if (!$wholeonly && isset($wholeonly) && $filterstr != '')
        $filterstr = "%" . $filterstr . "%";
    $sql = "SELECT COUNT(*) FROM (SELECT t1.`codigoContato`, lp0.`nome` AS `lp_codigoContato`, t1.`codigoTipoContato`, lp1.`descricao` AS `lp_codigoTipoContato` FROM `contatotipos` AS t1 LEFT OUTER JOIN `contato` AS lp0 ON (t1.`codigoContato` = lp0.`codigoContato`) LEFT OUTER JOIN `tipocontato` AS lp1 ON (t1.`codigoTipoContato` = lp1.`codigoTipoContato`)) subq";
    if (isset($filterstr) && $filterstr != '' && isset($filterfield) && $filterfield != '') {
        $sql .= " where " . sqlstr($filterfield) . " like '" . $filterstr . "'";
    } elseif (isset($filterstr) && $filterstr != '') {
        $sql .= " where (`lp_codigoContato` like '" . $filterstr . "') or (`lp_codigoTipoContato` like '" . $filterstr . "')";
    }
    $res = mysqli_query($conn, $sql) or die(mysql_error());
    $row = mysqli_fetch_assoc($res);
    reset($row);
    return current($row);
}

function sql_insert() {
    global $conn;
    global $_POST;

    $sql = "insert into `contatotipos` (`codigoContato`, `codigoTipoContato`) values (" . sqlvalue(@$_POST["codigoContato"], false) . ", " . sqlvalue(@$_POST["codigoTipoContato"], false) . ")";
    mysqli_query($conn, $sql) or die(mysql_error());
}

function sql_update() {
    global $conn;
    global $_POST;

    $sql = "update `contatotipos` set `codigoContato`=" . sqlvalue(@$_POST["codigoContato"], false) . ", `codigoTipoContato`=" . sqlvalue(@$_POST["codigoTipoContato"], false) . " where " . primarykeycondition();
    mysqli_query($conn, $sql) or die(mysql_error());
}

function sql_delete() {
    global $conn;

    $sql = "delete from `contatotipos` where " . primarykeycondition();
    mysqli_query($conn, $sql) or die(mysql_error());
}

function primarykeycondition() {
    global $_POST;
    $pk = "";
    $pk .= "(`codigoContato`";
    if (@$_POST["xcodigoContato"] == "") {
        $pk .= " IS NULL";
    } else {
        $pk .= " = " . sqlvalue(@$_POST["xcodigoContato"], false);
    };
    $pk .= ") and ";
    $pk .= "(`codigoTipoContato`";
    if (@$_POST["xcodigoTipoContato"] == "") {
        $pk .= " IS NULL";
    } else {
        $pk .= " = " . sqlvalue(@$_POST["xcodigoTipoContato"], false);
    };
    $pk .= ")";
    return $pk;
}
?>
