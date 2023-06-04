cat password_base_list.txt > inital-list
cat password_base_list.txt | sed 's/a/@/g' >> inital-list
cat password_base_list.txt | sed 's/e/3/g' >> inital-list
cat password_base_list.txt | sed 's/e/3/g' | sed 's/a/@/g' >> inital-list
cat password_base_list.txt | sed 's/a/@/g' | sed 's/s/$/g' >> inital-list
cat password_base_list.txt | sed 's/e/3/g' | sed 's/s/$/g' >> inital-list
cat password_base_list.txt | sed 's/e/3/g' | sed 's/a/@/g' | sed 's/s/$/g' >> inital-list
