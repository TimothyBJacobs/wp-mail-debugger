export default function SidebarItem( { title, slug, children } ) {
	return (
		<aside
			className={ `wmd-email-sidebar-item wmd-email-sidebar-item--${ slug }` }
		>
			<h3>{ title }</h3>
			{ children }
		</aside>
	);
}
