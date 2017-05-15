<?php

class Pagination
{
    var $total_pages = -1;//items
    var $limit = null;
    var $target = "";
    var $page = 1;
    var $adjacents = 2;
    var $showCounter = false;
    var $className = "pagination";
    var $parameterName = "page";
    var $urlF = false;//urlFriendly
    var $start;
    var $otherParam;


    var $nextT = "Next";
    var $nextI = "&raquo;"; //&#9658;
    var $prevT = "Previous";
    var $prevI = "&laquo;"; //&#9668;
    var $pagination;


    var $calculate = false;

    #Total items
    function items($value)
    {
        $this->total_pages = (int)$value;
    }

    #how many items to show per page
    function limit($value)
    {
        $this->limit = (int)$value;
    }

    #Page to sent the page value
    function target($value)
    {
        $this->target = $value;
    }

    function otherParam($value)
    {
        $this->otherParam = $value;
    }

    #Current page
    function currentPage($value)
    {
        $this->page = (int)$value;
    }

    #How many adjacent pages should be shown on each side of the current page?
    function adjacents($value)
    {
        $this->adjacents = (int)$value;
    }

    #show counter?
    function showCounter($value = "")
    {
        $this->showCounter = ($value === true) ? true : false;
    }

    #to change the class name of the pagination div
    function changeClass($value = "")
    {
        $this->className = $value;
    }

    function nextLabel($value)
    {
        $this->nextT = $value;
    }

    function nextIcon($value)
    {
        $this->nextI = $value;
    }

    function prevLabel($value)
    {
        $this->prevT = $value;
    }

    function prevIcon($value)
    {
        $this->prevI = $value;
    }

    #to change the class name of the pagination div
    function parameterName($value = "")
    {
        $this->parameterName = $value;
    }

    #to change urlFriendly
    function urlFriendly($value = "%")
    {
        if (preg_match('/^ *$/', $value)) {
            $this->urlF = false;
            return false;
        }
        $this->urlF = $value;
    }

    function pagination()
    {
    }

    function show()
    {
        if (!$this->calculate)
            if ($this->calculate())
                return "<nav><ul class=\"$this->className\">$this->pagination</ul></nav>\n";
    }

    function getLimitArr()
    {
        if ($this->page)
            $this->start = ($this->page - 1) * $this->limit;
        else
            $this->$start = 0;
        return array($this->start, $this->limit);
    }

    function getOutput()
    {
        if (!$this->calculate)
            if ($this->calculate())
                return "<nav><ul class=\"$this->className\">$this->pagination</ul></nav>\n";
    }

    function get_pagenum_link($id)
    {
        if (strpos($this->target, '?') === false)
            if ($this->urlF)
                return str_replace($this->urlF, $id, $this->target);
            else
                return "$this->target?$this->parameterName=$id";
        else
            return "$this->target&$this->parameterName=$id";
    }

    function calculate()
    {
        $this->pagination = "";
        $this->calculate == true;
        $error = false;
        if ($this->urlF and $this->urlF != '%' and strpos($this->target, $this->urlF) === false) {
            //Es necesario especificar el comodin para sustituir
            echo "Especificaste un wildcard para sustituir, pero no existe en el target<br />";
            $error = true;
        } elseif ($this->urlF and $this->urlF == '%' and strpos($this->target, $this->urlF) === false) {
            echo "Es necesario especificar en el target el comodin % para sustituir el n鷐ero de p醙ina<br />";
            $error = true;
        }

        if ($this->total_pages < 0) {
            echo "It is necessary to specify the <strong>number of pages</strong> (\$class->items(1000))<br />";
            $error = true;
        }
        if ($this->limit == null) {
            echo "It is necessary to specify the <strong>limit of items</strsong> to show per page (\$class->limit(10))<br />";
            $error = true;
        }
        if ($error) return false;

        $n = trim($this->nextT . ' ' . $this->nextI);
        $p = trim($this->prevI . ' ' . $this->prevT);


        $prev = $this->page - 1;                            //previous page is page - 1
        $next = $this->page + 1;
        $lastpage = ceil($this->total_pages / $this->limit);        //lastpage is = total pages / items per page, rounded up.
        $lpm1 = $lastpage - 1;                        //last page minus 1


        if ($lastpage > 1) {
            if ($this->page) {
                //anterior button
                if ($this->page > 1)
                    $this->pagination .= "<li><a href=\"javascript:void(0)\" onclick=changepage(\"" . $prev . "\") class=\"prev\">$p</a></li>";
                else
                    $this->pagination .= "<li class=\"disabled\"><a href=\"javascript:void(0)\" onclick=changepage(1) class=\"prev\">$p</li>";
            }
            //pages
            if ($lastpage < 7 + ($this->adjacents * 2)) {//not enough pages to bother breaking it up
                for ($counter = 1; $counter <= $lastpage; $counter++) {
                    if ($counter == $this->page)
                        $this->pagination .= "<li class=\"active\"><a href=\"javascript:void(0)\" onclick=changepage(\"" . $counter . "\")>$counter</a></li>";
                    else
                        $this->pagination .= "<li><a href=\"javascript:void(0)\" onclick=changepage(\"" . $counter . "\")>$counter</a></li>";
                }
            } elseif ($lastpage > 5 + ($this->adjacents * 2)) {//enough pages to hide some
                //close to beginning; only hide later pages
                if ($this->page < 1 + ($this->adjacents * 2)) {
                    for ($counter = 1; $counter < 4 + ($this->adjacents * 2); $counter++) {
                        if ($counter == $this->page)
                            $this->pagination .= "<li class=\"active\"><a href=\"javascript:void(0)\" onclick=changepage(\"" . $counter . "\")>$counter</a></li>";
                        else
                            $this->pagination .= "<li><a href=\"javascript:void(0)\" onclick=changepage(\"" . $counter . "\")>$counter</a></li>";
                    }
                    $this->pagination .= "<li class=\"disabled\"><a href=\"javascript:void(0)\">...</a></li>";
                    $this->pagination .= "<li><a href=\"javascript:void(0)\" onclick=changepage(\"" . $lpm1 . "\")>$lpm1</a></li>";
                    $this->pagination .= "<li><a href=\"javascript:void(0)\" onclick=changepage(\"" . $lastpage . "\")>$lastpage</a></li>";
                } //in middle; hide some front and some back
                elseif ($lastpage - ($this->adjacents * 2) > $this->page && $this->page > ($this->adjacents * 2)) {
                    $this->pagination .= "<li><a href=\"javascript:void(0)\" onclick=changepage(1)>1</a></li>";
                    $this->pagination .= "<li><a href=\"javascript:void(0)\" onclick=changepage(2)>2</a></li>";
                    $this->pagination .= "<li class=\"disabled\"><a href=\"\">...</a></li>";
                    for ($counter = $this->page - $this->adjacents; $counter <= $this->page + $this->adjacents; $counter++)
                        if ($counter == $this->page)
                            $this->pagination .= "<li class=\"active\"><a href=\"javascript:void(0)\" onclick=changepage(\"" . $counter . "\")>$counter</a></li>";
                        else
                            $this->pagination .= "<li><a href=\"javascript:void(0)\" onclick=changepage(\"" . $counter . "\")>$counter</a></li>";
                    $this->pagination .= "<li class=\"disabled\"><a href=\"javascript:void(0)\">...</a></li>";
                    $this->pagination .= "<li><a href=\"javascript:void(0)\" onclick=changepage(\"" . $lpm1 . "\")>$lpm1</a></li>";
                    $this->pagination .= "<li><a href=\"javascript:void(0)\" onclick=changepage(\"" . $lastpage . "\")>$lastpage</a></li>";
                } //close to end; only hide early pages
                else {
                    $this->pagination .= "<li><a href=\"javascript:void(0)\" onclick=changepage(1)>1</a></li>";
                    $this->pagination .= "<li><a href=\"javascript:void(0)\" onclick=changepage(2)>2</a></li>";
                    $this->pagination .= "<li class=\"disabled\"><a href=\"javascript:void(0)\">...</a></li>";
                    for ($counter = $lastpage - (2 + ($this->adjacents * 2)); $counter <= $lastpage; $counter++)
                        if ($counter == $this->page)
                            $this->pagination .= "<li class=\"active\"><a href=\"javascript:void(0)\" onclick=changepage(\"" . $counter . "\")>$counter</a></li>";
                        else
                            $this->pagination .= "<li><a href=\"javascript:void(0)\" onclick=changepage(\"" . $counter . "\")>$counter</a></li>";
                }
            }
            if ($this->page) {
                //siguiente button
                if ($this->page < $counter - 1)
                    $this->pagination .= "<li><a href=\"javascript:void(0)\" onclick=changepage(\"" . $next . "\") class=\"next\">$n</a></li>";
                else
                    $this->pagination .= "<li class=\"disabled\"><a href=\"javascript:void(0)\" onclick=changepage(\"" . $lastpage . "\") class=\"next\">$n</a></li>";
                if ($this->showCounter) $this->pagination .= "<nav><ul class=\"pagination\">($this->total_pages Pages)</ul></nav>";
            }
        }

        return true;
    }
}
