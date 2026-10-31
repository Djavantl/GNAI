import BusinessPage from '@/components/Navigation/BusinessPage/BusinessPage';
import type { ProfileEditProps } from './Profile.types';
import { dashboardUrl } from './Profile.utils';
import ProfileForm from './ProfileForm';
import './Profile.css';

export default function ProfileEditPage(props: ProfileEditProps): React.JSX.Element {
  return (
    <BusinessPage
      title="Meu Perfil"
      subtitle="Gerencie suas informações pessoais e segurança da conta."
      breadcrumbItems={[
        { label: 'Home', href: dashboardUrl },
        { label: 'Meu Perfil' },
      ]}
      className="profilePage"
    >
      <ProfileForm
        key={props.person.id}
        {...props}
      />
    </BusinessPage>
  );
}
