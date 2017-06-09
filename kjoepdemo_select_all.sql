use kjoepdemo;

select	*	from	association;
select	*	from	course;
select	*	from	portfolio;
select	*	from	courseowner;
select	*	from	person;
select	*	from	profile;
select	*	from	role;
select	*	from	roletype;
select	*	from	school;
select	*	from	user;
select	*	from	message;
select	*	from	mentorship;




select	school.name
,		person.username
,		role.role
from	school
,		person
,		role
,		association
where	school.id		= association.schoolid
and		role.id			= association.roleid
and		role.personid	= person.id
order by 1,3,2
