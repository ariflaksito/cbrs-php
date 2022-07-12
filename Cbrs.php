<?php

class Cbrs {
    
    private $num_docs = 0;
    private $corpus_terms = array();
    private $doc_weight = array();
    private $docs = array();

    function show_docs($doc) {
        $jumlah_doc = count($doc);
        for($i=0; $i < $jumlah_doc; $i++) {
            echo "Dokumen ke-$i : $doc[$i] <br /><br />";
        }
    }

    # Membuat index untuk terms dari semua dokumen
    function create_index($d) {
        $this->docs = $d;
        $this->num_docs = count($d);
        foreach($d as $k => $dv){
            $doc_terms = array();
            $doc_terms = explode(" ", $dv);
            
            $num_terms = count($doc_terms);
            for($j=0; $j < $num_terms; $j++) {
                $term = strtolower($doc_terms[$j]);
                $this->corpus_terms[$term][] = array($k, $j);
            }
        }
    }

    # Menampilkan hasil dari create_index()
    function show_index() {
        ksort($this->corpus_terms);
        foreach($this->corpus_terms AS $term => $doc_locations) {
            echo "<b>$term:</b>";
            echo "<br />";
            foreach($doc_locations AS $doc_location){
                echo "{".$doc_location[0].", ".$doc_location[1]."} ";
                echo "<br />";
            }
        }
    }

    # Menghitung nilai DF
    function df($term) {
        $d = array();
        $tr = $this->corpus_terms[$term];
        foreach($tr as $t)
            $d[] = $t[0];

        $dx = array_unique($d);
        return count($dx);
    }

    # Menghitung Nilai IDF
    function idf() {
        $ndf = [];
        foreach($this->corpus_terms as $t => $terms){
			$df  = $this->df($t);
            $ddf = $this->num_docs/$this->df($t);
            $idf = round(log10($ddf), 4);
        
            $ndf[$t][0] = $df;
            $ndf[$t][1] = $idf;
		}	

        return $ndf;
    }

    # Menghitung doc weight / TFIDF
    function weight(){
        $ndw = [];
        foreach($this->docs as $k=>$d){
            $dterm = explode(" ",$d);
            $dx = array_count_values($dterm);
            foreach($this->idf() as $t => $terms){
                if(empty($dx[$t]))
                    $ndw[$k][$t] = 0;    
                else $ndw[$k][$t] = $dx[$t] * $terms[1];
            }
        }
        $this->doc_weight = $ndw;
        return $ndw;
    }

    # Fungsi pencarian berdasar keyword
    # Pastikan keyword sudah melalui tahap pre-processing
    function search($keyword){
        
        $key = explode(" ", $keyword);
        $score = [];
        $i = 0;
        foreach($this->doc_weight as $ndw => $w){
            $score[$ndw] = 0;
            foreach($w as $wg => $v){
                foreach($key as $k){
                    if($k == $wg)
                    $score[$ndw] += $v;
                }   
            }
            $i++;
        }

        arsort($score);
        return $score;
    }

    # Fungsi menghitung similarity ke semua dokumen
    # parameter input = id dari item
    public function similarity($d1){
        $score = [];
        foreach($this->doc_weight as $ndw => $w){
            $score[$ndw] = $this->cosim($d1, $ndw);
        }

        arsort($score);
        return $score;
    }

    private function cosim($d1, $d2){
        $dw = $this->doc_weight;

        # sum square dari 2 doc
        $dw1 = $dw[$d1];
        $dw2 = $dw[$d2];
              
        $dx = 0;
        $dx1 = 0;
        $dx2 = 0;

        foreach($this->corpus_terms as $t => $terms){
            $dx += $dw1[$t] * $dw2[$t];
            $dx1 += $dw1[$t] * $dw1[$t];
            $dx2 += $dw2[$t] * $dw2[$t]; 
        }
        
        return round($dx / (sqrt($dx1) * sqrt($dx2)), 4);

    }


    
}