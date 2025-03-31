# 2FA Testing Checklist

## Setup Flow
- [ ] Prístup na /admin/2fa/setup zobrazuje QR kód
- [ ] QR kód je možné naskenovať Google Authenticator aplikáciou
- [ ] Generujú sa záložné kódy
- [ ] Verifikácia kódu pri aktivácii funguje
- [ ] Po úspešnej aktivácii je užívateľ presmerovaný správne

## Login Flow
- [ ] Pri prihlásení je vyžadovaný 2FA kód ak je 2FA aktívne
- [ ] Nesprávny kód je odmietnutý
- [ ] Správny kód umožní prístup
- [ ] Záložné kódy fungujú ako alternatíva
- [ ] Po použití je záložný kód zneplatnený

## Security Checks
- [ ] Session timeout funguje správne
- [ ] Rate limiting je aktívny
- [ ] 2FA nemožno obísť priamym prístupom na URL
- [ ] Záložné kódy sú bezpečne uložené
- [ ] Secret key je správne zašifrovaný

## Edge Cases
- [ ] Správne ošetrenie vypršanej session
- [ ] Správne ošetrenie neplatného QR kódu
- [ ] Správne ošetrenie neplatného záložného kódu
- [ ] Správne ošetrenie straty všetkých záložných kódov