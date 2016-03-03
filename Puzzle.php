<?php

class Puzzle {

    var $pos;
    var $sequencia;
    var $profundidade;
    var $val;
    var $caminho_perc;
    var $posobjetivo;

    function setInicial($pos_atual) {
        $this->pos = $this->arrayToNumber($pos_atual);
        $this->profundidade = 1;
        $this->sequencia[] = $this->pos;
    }

    function setObjetivo($pos_objetivo) {
        $this->posobjetivo = $this->arrayToNumber($pos_objetivo);
        $this->avaliar($pos_objetivo);
    }
    //testa posição objetivo, se posição estiver no objetivo retorna verdadeiro
    function objetivoTeste() {
        if ($this->pos == $this->posobjetivo) {
            return True;
        } else {
            return False;
        }
    }
    // verifica os movimentos 
    function possivelMovimentos() {
        $Movimentos = array();
        $pos_atual = $this->numberToArray($this->pos);
        for ($i = 0; $i < 3; $i++) {
            for ($j = 0; $j < 3; $j++) {
                if ($pos_atual[$i][$j] == 0) {
                    break 2;
                }
            }
        }

        $this->verificaMovimento($i, $j, $i - 1, $j, $pos_atual, $Movimentos);
        $this->verificaMovimento($i, $j, $i + 1, $j, $pos_atual, $Movimentos);
        $this->verificaMovimento($i, $j, $i, $j - 1, $pos_atual, $Movimentos);
        $this->verificaMovimento($i, $j, $i, $j + 1, $pos_atual, $Movimentos);

        return $Movimentos;
    }
    
    //move a peça 0 ou branca
    function moveBranco($srcRow, $srcCol, $destRow, $destCol, $newpos) {
        $tmp = $newpos[$destRow][$destCol];
        $newpos[$destRow][$destCol] = $newpos[$srcRow][$srcCol];
        $newpos[$srcRow][$srcCol] = $tmp;
        return $newpos;
    }

    function EmSequencia($pos) {
        foreach ($this->sequencia as $seqpos) {
            if ($seqpos === $pos) {
                return TRUE;
            }
        }
        return FALSE;
    }

    function Mover($srcLin, $srcCol, $destLin, $destCol) {
        if ($srcLin < 0 or $srcCol < 0 or $destLin < 0 or $destCol < 0) {
            return FALSE;
        }
        if ($srcLin > 2 or $srcCol > 2 or $destLin > 2 or $destCol > 2) {
            return FALSE;
        }
        return TRUE;
    }

    function verificaMovimento($srcLin, $srcCol, $destLin, $destCol, $pos_atual, & $Movimentos) {
        if ($this->Mover($srcLin, $srcCol, $destLin, $destCol)) {
            $novapos = $this->moveBranco($srcLin, $srcCol, $destLin, $destCol, $pos_atual);
            $posnum = $this->arrayToNumber($novapos);
            if ($this->EmSequencia($posnum) == FALSE) {
                $novoMov = clone $this;
                $novoMov->pos = $posnum;
                $novoMov->sequencia[] = $posnum;
                $novoMov->profundidade++;
                $novoMov->avaliar($novapos);
                $Movimentos[] = $novoMov;
            }
        }
    }
    //imprime as posições na tabela
    function imprimePos($pos) {
        print("<table border='3'");    
         for ($i = 0; $i < 3; $i++) {             
             print("<tr>");
            for ($j = 0; $j < 3; $j++) {                
                print("<td>" . $pos[$i][$j] . "</td> ");                
            }
            print("</tr>");           
        }       
       print("</table>");
       print("<br>");
    }
 
    function imprimeSequencia() {
      
        
        for ($i = 0; $i < count($this->sequencia); $i++) {
            
            print ("Passo $i    ");
            $pos = $this->numberToarray($this->sequencia[$i]);
            $this->imprimePos($pos);
            print("<br>");
        }
      
    }

    

    function imprimeReverso() {
        for ($i = ($this->profundidade - 1); $i >= 0; $i--) {
            print ("Passo $i <br>   ");
            $pos = $this->numberToArray($this->sequencia[$i]);
            $this->imprimePos($pos);
            print("<br>");
        }
    }
    
    function heuristica($pos) {
        $posobjetivo = $this->numberToarray($this->posobjetivo);
        $this->val = 0;
        for ($i = 0; $i < 3; $i++) {
            for ($j = 0; $j < 3; $j++) {
                $blocoLin = 0;
                $blocoCol = 0;
                $this->encontraBloco($posobjetivo[$i][$j], $blocoLin, $blocoCol, $pos);
                $blocoVal = abs($blocoLin - $i) + abs($blocoCol - $j);
                $this->val = $this->val + $blocoVal;
            }
        }
    }

    function avaliar($pos) {
        $this->heuristica($pos);
        $this->caminhoPerc();
    }



    function caminhoPerc() {
        $this->caminho_perc = $this->profundidade;
    }

    
    function encontraBloco($val, &$i, &$j, $pos) {
        for ($i = 0; $i < 3; $i++) {
            for ($j = 0; $j < 3; $j++) {
                if ($pos[$i][$j] == $val) {
                    break 2;
                }
            }
        }
    }

    function arrayToString($posarray) {
        $posstr = "";
        for ($i = 0; $i < 3; $i++) {
            $s = implode(",", $posarray[$i]);
            $posstr = $posstr . "|" . $s;
        }
        return $posstr;
    }

    function stringToarray($posstr) {

        $posarray = array();
        $itemArray = explode("|", $posstr);
        for ($i = 1; $i < count($itemArray); $i++) {
            $posarray[] = explode(",", $itemArray[$i]);
        }
        return $posarray;
    }

    function arrayToNumber($posarray) {
        $posnum = 0;
        $multiplica = 100000000;
        for ($i = 0; $i < 3; $i++) {
            for ($j = 0; $j < 3; $j++) {
                $posnum = $posnum + $posarray[$i][$j] * $multiplica;
                $multiplica = $multiplica / 10;
            }
        }
        return $posnum;
    }

    function numberToArray($posnum) {
        $posarray = array();
        $divisor = 100000000;
        for ($i = 0; $i < 3; $i++) {
            for ($j = 0; $j < 3; $j++) {
                $posarray[$i][$j] = (int) ($posnum / $divisor);
                $posnum = $posnum % $divisor;
                $divisor = $divisor / 10;
            }
        }
        return $posarray;
    }

}
