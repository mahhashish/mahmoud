<?php
// Implement the Pearson coefficient of correlation algorithm

function correlation($datax, $datay) {
        $meanx=mean($datax);
        $meany=mean($datay);
        $sigmaxy=0;
        $sigmaxsq=0;
        $sigmaysq=0;
        $xtot=0;
        $ytot=0;
        for ($i=0; $i<sizeof($datax); $i++) {
                $sigmaxsq+=($datax[$i]-$meanx)*($datax[$i]-$meanx);
                $sigmaysq+=($datay[$i]-$meany)*($datay[$i]-$meany);
                $sigmaxy+=$datax[$i]*$datay[$i];
                $xtot+=$datax[$i];
                $ytot+=$datay[$i];
        }
        $sigmaxy=$sigmaxy-($xtot*$ytot)/sizeof($datax);
        return $sigmaxy/(sqrt($sigmaxsq*$sigmaysq));
}

?>
