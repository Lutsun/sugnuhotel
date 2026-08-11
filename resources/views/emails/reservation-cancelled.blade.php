<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <title>Réservation annulée</title>
</head>
<body style="margin:0; padding:0; background-color:#f4f4f5; font-family: Arial, Helvetica, sans-serif; color:#27272a;">
    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="padding:24px 0;">
        <tr>
            <td align="center">
                <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="max-width:560px; background:#ffffff; border-radius:8px; overflow:hidden;">
                    <tr>
                        <td style="background-color:#b45309; padding:24px 32px;">
                            <h1 style="margin:0; color:#ffffff; font-size:20px;">SugnuHotel</h1>
                        </td>
                    </tr>
                    <tr>
                        <td style="padding:32px;">
                            <h2 style="margin-top:0; font-size:18px; color:#b91c1c;">Réservation annulée</h2>
                            <p>Bonjour {{ $reservation->user->name }},</p>
                            <p>Votre réservation <strong>{{ $reservation->reservation_number }}</strong> a bien été annulée.</p>

                            <table role="presentation" width="100%" cellpadding="8" cellspacing="0" style="border:1px solid #e4e4e7; border-radius:6px; margin:16px 0;">
                                <tr>
                                    <td style="color:#71717a;">Chambre</td>
                                    <td style="text-align:right;">{{ $reservation->room->room_number }} — {{ $reservation->room->roomType->name }}</td>
                                </tr>
                                <tr>
                                    <td style="color:#71717a;">Arrivée prévue</td>
                                    <td style="text-align:right;">{{ \Carbon\Carbon::parse($reservation->check_in_date)->translatedFormat('d F Y') }}</td>
                                </tr>
                                <tr>
                                    <td style="color:#71717a;">Départ prévu</td>
                                    <td style="text-align:right;">{{ \Carbon\Carbon::parse($reservation->check_out_date)->translatedFormat('d F Y') }}</td>
                                </tr>
                            </table>

                            <p>Si vous n'êtes pas à l'origine de cette annulation, contactez-nous rapidement.</p>
                            <p style="color:#71717a; font-size:13px;">L'équipe SugnuHotel</p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
