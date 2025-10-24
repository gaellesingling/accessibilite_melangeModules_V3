<?php
class Braille_Non_Contracted_Wrapper {
    public function translate($text) {
        // Table complète pour le braille non contracté (Grade 1)
        $map = [
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

        $output = '';
        for ($i = 0; $i < mb_strlen($text); $i++) {
            $char = mb_substr($text, $i, 1);

            // Gestion des majuscules
            if (ctype_upper($char)) {
                $lowerChar = mb_strtolower($char);
                $output .= '⠠' . ($map[$lowerChar] ?? '?');
            }
            // Caractère normal
            else {
                $output .= $map[$char] ?? '?';
            }
        }

        return $this->format_output($output);
    }

    private function format_output($braille_text) {
        if (empty($braille_text)) {
            return new WP_Error('empty_output', __('Aucune sortie de traduction', 'braille-non-contracted'));
        }

        $output = '<div class="braille-output" role="region" aria-label="' .
                 esc_attr__('Traduction en braille non contracté', 'braille-non-contracted') . '">';

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
                  esc_html__('Fin de la traduction braille', 'braille-non-contracted') .
                  '</span></div>';
        return $output;
    }
}
