use Symfony\Component\DependencyInjection\Definition;

$container->setParameter('app.verify_email.username', 'labroots_nathan');
$container->setParameter('app.verify_email.password', 'wwvvlqhc7tfmpl');

$container->setDefinition('app.verify_email', new Definition(
    'Blogger\BlogBundle',
    array('verify_email')
));
