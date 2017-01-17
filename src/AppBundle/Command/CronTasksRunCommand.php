<?php

namespace AppBundle\Command;


use AppBundle\Entity\User;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class CronTasksRunCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('crontasks:run')
            ->setDescription('Runs Cron Tasks if needed')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln('<comment>Running Cron Tasks...</comment>');
        $output->writeln('<info>email</info>');
        $this->_runEmailSender();
        $output->writeln('<comment>Done!</comment>');
    }

    private function _runEmailSender()
    {
        $today = new \DateTime();
        $em = $this->getContainer()->get('doctrine.orm.entity_manager');
        $users = $em->getRepository('AppBundle:User')->findBy(
            ['wantNewsletter' => true]
        );
        $articles = $em->getRepository('AppBundle:Article')->findTodaysBest(
            $today,
            5
        );
        foreach ($users as $user) {
            $this->_sendNewsletter($user, $articles);
        }
    }

    /**
     * @param $user
     */
    private function _sendNewsletter(User $user, $articles)
    {
        $mail = \Swift_Message::newInstance();
        $mail->setFrom('itran.newscast@gmail.com')
            ->setTo($user->getEmail())
            ->setSubject('Everyday news from Newscast')
            ->setBody(
                $this->getContainer()->get('templating')->render(
                    'newsletter/newsletter.html.twig',
                    ['username' => $user->getUsername(),
                        'articles' => $articles]
                ), 'text/html'
            );

        $this->getContainer()->get('mailer')->send($mail);
    }
}
