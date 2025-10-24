<?php
class Braille_Contracted_Wrapper {
    public function translate($text) {
        // Table de base pour les caractères individuels
        $base_map = [
            // Lettres minuscules
            'a' => '⠁', 'b' => '⠃', 'c' => '⠉', 'd' => '⠙', 'e' => '⠑',
            'f' => '⠋', 'g' => '⠛', 'h' => '⠓', 'i' => '⠊', 'j' => '⠚',
            'k' => '⠅', 'l' => '⠇', 'm' => '⠍', 'n' => '⠝', 'o' => '⠕',
            'p' => '⠏', 'q' => '⠟', 'r' => '⠗', 's' => '⠎', 't' => '⠞',
            'u' => '⠥', 'v' => '⠧', 'w' => '⠺', 'x' => '⠭', 'y' => '⠽',
            'z' => '⠵',

            // Majuscules (précédées de ⠠)
            'A' => '⠠⠁', 'B' => '⠠⠃', 'C' => '⠠⠉', 'D' => '⠠⠙', 'E' => '⠠⠑',
            'F' => '⠠⠋', 'G' => '⠠⠛', 'H' => '⠠⠓', 'I' => '⠠⠊', 'J' => '⠠⠚',
            'K' => '⠠⠅', 'L' => '⠠⠇', 'M' => '⠠⠍', 'N' => '⠠⠝', 'O' => '⠠⠕',
            'P' => '⠠⠏', 'Q' => '⠠⠟', 'R' => '⠠⠗', 'S' => '⠠⠎', 'T' => '⠠⠞',
            'U' => '⠠⠥', 'V' => '⠠⠧', 'W' => '⠠⠺', 'X' => '⠠⠭', 'Y' => '⠠⠽',
            'Z' => '⠠⠵',

            // Ponctuation et symboles
            ' ' => ' ', '.' => '⠲', ',' => '⠂', ';' => '⠆', ':' => '⠒',
            '!' => '⠖', '?' => '⠦', '"' => '⠐⠄', '\'' => '⠄', '(' => '⠐⠣',
            ')' => '⠐⠜', '[' => '⠨⠣', ']' => '⠨⠜', '{' => '⠸⠣',
            '}' => '⠸⠜', '-' => '⠤', '_' => '⠸⠤', '/' => '⠌', '\\' => '⠸⠌',
            '@' => '⠈⠁', '#' => '⠼', '$' => '⠈⠎', '%' => '⠨⠴', '&' => '⠈⠯',
            '*' => '⠐⠔', '+' => '⠐⠖', '=' => '⠐⠶', '|' => '⠸⠳', '~' => '⠐⠐',
            '`' => '⠈⠄', '^' => '⠘',

            // Symboles spéciaux
            '€' => '⠈⠑', '§' => '⠨⠎', '¶' => '⠨⠏', '†' => '⠨⠞', '‡' => '⠨⠉',
            '•' => '⠐⠂', '…' => '⠐⠆', '©' => '⠨⠉', '®' => '⠨⠗', '™' => '⠨⠞⠍',
            '«' => '⠐⠦', '»' => '⠐⠴', '‘' => '⠄', '’' => '⠄', '“' => '⠐⠄',
            '”' => '⠐⠄', '–' => '⠤', '—' => '⠸⠤',

            // Chiffres
            '0' => '⠼⠴', '1' => '⠼⠁', '2' => '⠼⠃', '3' => '⠼⠉', '4' => '⠼⠙',
            '5' => '⠼⠑', '6' => '⠼⠋', '7' => '⠼⠛', '8' => '⠼⠓', '9' => '⠼⠊',

            // Accents français
            'à' => '⠁', 'â' => '⠡', 'ä' => '⠡', 'é' => '⠑', 'è' => '⠑',
            'ê' => '⠑', 'ë' => '⠑', 'î' => '⠊', 'ï' => '⠊', 'ô' => '⠕',
            'ö' => '⠕', 'ù' => '⠥', 'û' => '⠥', 'ü' => '⠳', 'ç' => '⠉',
            'œ' => '⠡⠑', 'æ' => '⠡⠑', 'Æ' => '⠠⠡⠑', 'Œ' => '⠠⠡⠑'
        ];

        // Contractions spécifiques au français (Grade 2)
        $contractions = [
            'ch' => '⠡', 'ou' => '⠳', 'st' => '⠌', 'au' => '⠡⠥', 'ea' => '⠑',
            'ui' => '⠥⠊', 'en' => '⠑⠝', 'in' => '⠊⠝', 'an' => '⠁⠝', 'oi' => '⠕⠊',
            'on' => '⠕⠝', 'un' => '⠥⠝', 'eu' => '⠑⠥', 'qu' => '⠟⠥', 'gu' => '⠛⠥',
            'gn' => '⠛⠝', 'ph' => '⠏⠓', 'th' => '⠞⠓', 'oin' => '⠕⠊⠝',
            'ien' => '⠊⠑⠝', 'ion' => '⠊⠕⠝', 'eau' => '⠑⠡', 'eaux' => '⠑⠡',
            'aux' => '⠡⠥', 'eux' => '⠑⠥', 'eur' => '⠑⠥⠗',

            // Mots courants contractés
            'le' => '⠇', 'la' => '⠇', 'les' => '⠇⠎', 'de' => '⠙', 'des' => '⠙⠎',
            'et' => '⠑', 'en' => '⠑⠝', 'dans' => '⠙⠁⠝⠎', 'pour' => '⠏⠥⠗',
            'par' => '⠏⠗', 'sur' => '⠎⠥⠗', 'avec' => '⠁⠧⠉', 'mais' => '⠍⠁⠊⠎',
            'ou' => '⠳', 'qui' => '⠟⠥⠊', 'que' => '⠟⠥⠑', 'pas' => '⠏⠁⠎',
            'plus' => '⠏⠇⠥⠎', 'comme' => '⠉⠕⠍⠍⠑',

            // Contractions supplémentaires
            'ce' => '⠉⠑', 'ci' => '⠉⠊', 'je' => '⠚⠑', 'me' => '⠍⠑',
            'ne' => '⠝⠑', 'se' => '⠎⠑', 'te' => '⠞⠑', 'du' => '⠙⠥',
            'ses' => '⠎⠑⠎', 'ces' => '⠉⠑⠎', 'mes' => '⠍⠑⠎', 'tes' => '⠞⠑⠎'
        ];

        $output = '';
        $i = 0;
        $len = mb_strlen($text);

        while ($i < $len) {
            $currentChar = mb_substr($text, $i, 1);
            $nextChar = ($i + 1 < $len) ? mb_substr($text, $i + 1, 1) : '';

            // Gestion des majuscules
            if (ctype_upper($currentChar)) {
                $lowerChar = mb_strtolower($currentChar);
                $output .= '⠠' . ($base_map[$lowerChar] ?? '?');
                $i++;
                continue;
            }

            // Vérifier les contractions (paires de caractères)
            $pair = $currentChar . $nextChar;
            if (isset($contractions[$pair])) {
                $output .= $contractions[$pair];
                $i += 2;
                continue;
            }

            // Caractère simple
            $output .= $base_map[$currentChar] ?? '?';
            $i++;
        }

        return $this->format_output($output);
    }

    private function format_output($braille_text) {
        if (empty($braille_text)) {
            return new WP_Error('empty_output', __('Aucune sortie de traduction', 'braille-contracted'));
        }

        $output = '<div class="braille-output" role="region" aria-label="' .
                 esc_attr__('Traduction en braille contracté', 'braille-contracted') . '">';

        $chars = preg_split('//u', $braille_text, -1, PREG_SPLIT_NO_EMPTY);
        foreach ($chars as $char) {
            $code = mb_ord($char);
            if ($code >= 0x2800 && $code <= 0x28FF) {
                $output .= '<span class="braille-char" aria-hidden="true">' . $char . '</span>';
            } else {
                $output .= $char;
            }
        }

        $output .= '<span class="sr-only">' .
                  esc_html__('Fin de la traduction braille', 'braille-contracted') .
                  '</span></div>';
        return $output;
    }
}
