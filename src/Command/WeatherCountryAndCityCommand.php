<?php

namespace App\Command;

use App\Entity\Measurement;
use App\Service\WeatherUtil;
use Symfony\Component\Console\Attribute\Argument;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'weather:country-and-city',
    description: 'Add a short description for your command',
)]
class WeatherCountryAndCityCommand{
    public function __construct(
        private readonly WeatherUtil $weatherUtil
    ) {
    }
    public function __invoke(
        SymfonyStyle $io,
        #[Argument('Country Code')] string $countryCode,
        #[Argument('City')] string $city,
    ): int {
        $measurements = $this->weatherUtil->getWeatherForCountryAndCity($countryCode, $city);

        $io->writeln(sprintf('Weather for %s, %s:', $city, $countryCode));
        $io->table(
            ['Date', 'Temperature', 'Humidity'],
            array_map(fn(Measurement $m) => [
                $m->getDate()->format('Y-m-d H:i:s'),
                $m->getTemperature(),
                $m->getHumidity()
            ], $measurements)
        );
        return Command::SUCCESS;
    }
}
