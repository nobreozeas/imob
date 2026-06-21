import {
    LayoutDashboard,
    Building2,
    Users,
    FileText,
    DollarSign,
    Settings,
    type LucideIcon,
} from 'lucide-vue-next';

export interface NavItem {
    label: string;
    icon: LucideIcon;
    routeName: string;
    href: string;
}

export interface NavGroup {
    title: string;
    items: NavItem[];
}

export const NAV_GROUPS: NavGroup[] = [
    {
        title: 'Geral',
        items: [
            {
                label: 'Dashboard',
                icon: LayoutDashboard,
                routeName: 'dashboard',
                href: '/dashboard',
            },
        ],
    },
    {
        title: 'Gestão',
        items: [
            {
                label: 'Imóveis',
                icon: Building2,
                routeName: 'imoveis.index',
                href: '/imoveis',
            },
            {
                label: 'Clientes',
                icon: Users,
                routeName: 'clientes.index',
                href: '/clientes',
            },
            {
                label: 'Contratos',
                icon: FileText,
                routeName: 'contratos.index',
                href: '/contratos',
            },
        ],
    },
    {
        title: 'Financeiro',
        items: [
            {
                label: 'Recebimentos',
                icon: DollarSign,
                routeName: 'recebimentos.index',
                href: '/recebimentos',
            },
        ],
    },
    {
        title: 'Sistema',
        items: [
            {
                label: 'Configurações',
                icon: Settings,
                routeName: 'configuracoes.index',
                href: '/configuracoes',
            },
        ],
    },
];

export function useNavigation() {
    return { NAV_GROUPS };
}
