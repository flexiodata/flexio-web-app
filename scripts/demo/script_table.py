
import sys

def flexio_file_handler(input,output):

    # make a copy of the table
    input.fetch_style = dict
    rows = []
    for row in input:
      rows.append(row)

    # make a lookup table based on email
    lookup = {}
    for row in rows:
        lower_email = row['email'].lower()
        if row['shipping_method'].upper() != 'CONJOINT':
            lookup[lower_email] = row

    # replace where shipping_method is CONJOINT
    for row in rows:
        lower_email = row['email'].lower()
        if row['shipping_method'].upper() == 'CONJOINT':
            if lower_email in lookup:
                row['order_number'] = lookup[lower_email]['order_number']
                row['shipping_method'] = lookup[lower_email]['shipping_method']
                row['shipping_comment'] = lookup[lower_email]['shipping_comment']

    # create output table
    tbl = output.create(structure=input.structure)
    for row in rows:
        output.write(row)
