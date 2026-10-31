import BusinessPage from '@/components/Navigation/BusinessPage/BusinessPage';
import AccountProfileForm from './AccountProfileForm';
import type { ProfileAccountProps } from './Profile.types';
import { dashboardUrl } from './Profile.utils';
import './Profile.css';

export default function ProfileAccountPage(props: ProfileAccountProps): React.JSX.Element {
  return (
    <BusinessPage
      title="Meu Perfil"
      subtitle="Gerencie sua foto, seus dados de acesso e a segurança da conta."
      breadcrumbItems={[
        { label: 'Home', href: dashboardUrl },
        { label: 'Meu Perfil' },
      ]}
      className="profilePage"
    >
      <AccountProfileForm
        key={props.account.id}
        {...props}
      />
    </BusinessPage>
  );
}
