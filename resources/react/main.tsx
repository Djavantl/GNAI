import { createRoot } from 'react-dom/client';
import './styles/reset.css';
import './styles/tokens.css';
import './styles/globals.css';
import App from './app/App';

const root = document.getElementById('root');
if (!root) throw new Error('Elemento raiz do React não encontrado.');
createRoot(root).render(<App />);
