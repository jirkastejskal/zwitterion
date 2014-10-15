<?php
class ZwMedia
{
    static public function zmena_velikost_jpg($vstupni_nazev_souboru,$vystupni_nazev_souboru,$nove_x,$nove_y)
    {
        $image_orig_size = getimagesize($vstupni_nazev_souboru);  //Nacteni velikosti puvodniho obrazku(pole)
        $pomer = $image_orig_size[0] / $image_orig_size[1];
        if ($nove_x == 0) {
            $nove_x = $pomer*$nove_y;
        } else {
            $nove_y = $nove_x/$pomer;
        }
        $image_orig = imagecreatefromjpeg($vstupni_nazev_souboru);  //Vytvoreni originalniho obrazku

        $image_small = imagecreatetruecolor($nove_x, $nove_y);  //Vytvori novy obrazek(cerny s pouzitim TrueColor!) -  o dane velikosti

        imagecopyresampled($image_small, $image_orig, 0, 0, 0, 0, $nove_x, $nove_y, $image_orig_size[0], $image_orig_size[1]);  //Vytvori obr. dane velikosti z originalniho obrazku, bez posunuti
        imagejpeg($image_small,$vystupni_nazev_souboru, 85);  //Ulozeni obr. v kvalite 85 JPEG na prislusne misto

        imagedestroy($image_small);
        imagedestroy($image_orig);
    }

    static public function SmazObrazek ($jmeno, $cesta)
    {
    }

}