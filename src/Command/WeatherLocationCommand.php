<?php

namespace App\Command;

use App\Entity\Measurement;
use App\Repository\LocationRepository;
use App\Service\WeatherUtil;
use Symfony\Component\Console\Attribute\Argument;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Style\SymfonyStyle;


#[AsCommand(
    name: 'weather:location',
    description: 'Add a short description for your command',
)]
class WeatherLocationCommand{
    public function __construct(
        private readonly LocationRepository $locationRepository,
        private readonly WeatherUtil $weatherUtil
    ) {
    }
    public function __invoke(
        SymfonyStyle $io,
        #[Argument('Location ID')] int $id,
    ): int {
        $location = $this->locationRepository->find($id);

        $measurements = $this->weatherUtil->getWeatherForLocation($location);

        $io->writeln(sprintf('Location %s:', $location->getCity()));
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
