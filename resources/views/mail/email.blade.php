<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Réinitialisation du mot de passe</title>
</head>
<body style="font-family: Arial, sans-serif; background-color: #f5f5f5; margin: 0; padding: 0;">
  <table align="center" width="100%" cellpadding="0" cellspacing="0" style="max-width: 600px; background-color: #ffffff; border-radius: 6px; overflow: hidden; border: 1px solid #ddd;">
    
    <!-- Header avec logo -->
    <tr>
      <td style="background-color: #ffffff; padding: 20px; text-align: center; border-bottom: 3px solid #009640;">
        <img src="https://www.ofppt.ma/sites/default/files/logo_ofppt.png" alt="Logo OFPPT" style="width: 140px; height: auto;">
      </td>
    </tr>

    <!-- Corps du message -->
    <tr>
      <td style="padding: 30px;">
        <h2 style="color: #009640; text-align: center; margin-bottom: 25px; font-weight: bold;">Réinitialisation du mot de passe</h2>
        
        <p style="font-size: 15px; color: #333; line-height: 1.6;">
          Bonjour,
        </p>

        <p style="font-size: 15px; color: #333; line-height: 1.6;">
          Voici votre code de réinitialisation :
        </p>

        <!-- Code encadré -->
        <div style="background: #e8f5e9; border: 2px solid #009640; padding: 15px 25px; text-align: center; margin: 20px auto; font-size: 24px; font-weight: bold; color: #009640; letter-spacing: 4px; display: inline-block; border-radius: 4px;">
          {{ $username }}
        </div>

        <p style="font-size: 15px; color: #555; line-height: 1.6;">
          Ce code est valide pendant <strong>10 minutes</strong>.<br>
          Si vous n'avez pas demandé cette réinitialisation, veuillez ignorer cet e-mail.
        </p>

        <!-- Bouton action -->
        <div style="text-align: center; margin-top: 30px;">
          <a href="{{ route('forgot.password.verify') }}" style="background-color: #009640; color: #ffffff; padding: 12px 20px; text-decoration: none; border-radius: 4px; font-size: 15px; font-weight: bold;">
            Réinitialiser mon mot de passe
          </a>
        </div>
      </td>
    </tr>

    <!-- Pied de page -->
    <tr>
      <td style="background-color: #f5f5f5; padding: 15px; text-align: center; font-size: 12px; color: #777; border-top: 1px solid #ddd;">
        © {{ date('Y') }} OFPPT. Tous droits réservés.
      </td>
    </tr>
  </table>
</body>
</html>
