namespace App\Context;

use ApiPlatform\Core\Bridge\Doctrine\Orm\Util\QueryNameGeneratorInterface;
use ApiPlatform\Core\Serializer\Export\ContextTrait;

final class AppointmentExportContext implements QueryNameGeneratorInterface
{
    use ContextTrait;

    public function __construct(private string $token)
    {
    }

    public function getToken(): string
    {
        return $this->token;
    }
}
