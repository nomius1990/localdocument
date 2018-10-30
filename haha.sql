select org_name,l.`name`,position,t.name as export_type from bs_gb_expert as l 
inner join bs_bdata as t on l.btype = t.id  where l.status = 1 and l.ctype = 1 into outfile '/Users/boli/Documents/sql/1.txt';


select main.name, main.link_name, main.link_mobile, main.level,group_concat(data.name) from bs_senior_service as main
left join bs_senior_service_content as con on main.id = con.senior_service_id
left join bs_bdata as data on con.content_id = data.id and data.status = 1 
GROUP BY main.id,con.senior_service_id into outfile '/Users/boli/Documents/sql/2.txt';


drop table if EXISTS temp_company;
create TEMPORARY table temp_company
select  c.id as cid ,ind.id as indid   from bs_senior_company  as c inner join bs_senior_industry as ind 
	  on c.member_id = ind.member_id and c.id = ind.senior_id;

select c.name,reg_capital,reg_date,legal_rep,c.link_name,i.name,GROUP_CONCAT(d.name) as cctype 
from bs_senior_company as c 
left join bs_member_incubator as i  on c.incubator_id = i.id 
left join temp_company  as temp on c.id = temp.cid 
left join bs_bdata as d on temp.indid = d.id 
GROUP BY temp.cid into outfile '/Users/boli/Documents/sql/3.txt';