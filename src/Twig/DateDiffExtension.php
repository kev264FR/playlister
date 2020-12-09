<?php

namespace App\Twig;

use DateTime;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;
use Twig\TwigFunction;

class DateDiffExtension extends AbstractExtension
{
    public function getFilters(): array
    {
        return [
            // If your filter generates SAFE HTML, you should add a third
            // parameter: ['is_safe' => ['html']]
            // Reference: https://twig.symfony.com/doc/2.x/advanced.html#automatic-escaping
            new TwigFilter('filter_name', [$this, 'doSomething']),
        ];
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('dateDiff', [$this, 'calculateDate']),
        ];
    }

    public function calculateDate($date)
    {
        $curDate = new DateTime();
        $int = $date->diff($curDate);
        if ($int->format('%y') != 0) {
            return $int->format('%y an(s)');
        }elseif ($int->format('%m') != 0) {
            return $int->format('% mois');
        }elseif ($int->format('%d') != 0) {
            return $int->format('%d jour(s)');
        }elseif ($int->format('%h') != 0) {
            return $int->format('%h heure(s)');
        }elseif ($int->format('%i') != 0) {
            return $int->format('%i minute(s)');
        }elseif ($int->format('%s') != 0) {
            return $int->format('%s seconde(s)');
        }else{
            return '1 seconde';
        }
    }
}
