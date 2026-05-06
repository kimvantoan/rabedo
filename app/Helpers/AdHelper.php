<?php

namespace App\Helpers;

class AdHelper
{
    /**
     * Insert lazy-loaded ad placeholders evenly into HTML content.
     *
     * @param string $content
     * @param int $numberOfAds
     * @return string
     */
    public static function insertAds($content, $numberOfAds = 5)
    {
        if (empty($content) || $numberOfAds <= 0) {
            return $content;
        }

        // Split the content by closing paragraph tags
        $paragraphs = explode('</p>', $content);
        $totalParagraphs = count($paragraphs) - 1; 

        if ($totalParagraphs < 2) {
            return $content;
        }

        // Adjust the number of ads if the content is shorter than the desired ads
        $numberOfAds = min($numberOfAds, $totalParagraphs - 1);
        if ($numberOfAds <= 0) {
            return $content;
        }

        // Calculate the step (how many paragraphs between ads)
        $step = floor($totalParagraphs / ($numberOfAds + 1));
        if ($step < 1) {
            $step = 1;
        }

        $adPlaceholder = '
        <div class="ad-container my-8 flex justify-center w-full overflow-hidden" style="text-align: center; min-height: 250px;">
            <ins class="adsbygoogle"
                style="display:block; text-align:center;"
                data-ad-layout="in-article"
                data-ad-format="fluid"
                data-ad-client="ca-pub-4370452252708446"
                data-ad-slot="9674028583"></ins>
            <script>
                 (adsbygoogle = window.adsbygoogle || []).push({});
            </script>
        </div>';

        $newContent = '';
        $adsInserted = 0;

        foreach ($paragraphs as $index => $p) {
            $newContent .= $p;
            
            // Re-add the closing tag that was removed by explode
            if ($index < count($paragraphs) - 1) {
                $newContent .= '</p>';
            }

            // Insert ad placeholder after 'step' paragraphs
            // Prevent inserting right at the very end if not necessary to make it look natural
            if (($index + 1) % $step == 0 && $adsInserted < $numberOfAds && $index < $totalParagraphs - 1) {
                $newContent .= $adPlaceholder;
                $adsInserted++;
            }
        }

        return $newContent;
    }
}
